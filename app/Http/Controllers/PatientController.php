<?php

namespace App\Http\Controllers;

use App\Contracts\PatientRepositoryInterface;
use App\Enums\DomiciliaryReason;
use App\Models\PatientGosForm;
use App\Services\GosEligibilityService;
use App\Enums\DroppedReason;
use App\Enums\HowHeard;
use App\Enums\PatientTitle;
use App\Enums\PatientType;
use App\Enums\SexGender;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Pct;
use App\Models\Practice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function __construct(private readonly PatientRepositoryInterface $patients)
    {
    }

    /**
     * Find Patient screen — search form + paginated results.
     */
    public function index(Request $request): View
    {
        // Merge so that 'id_desc' (Latest) is the default when no sort param is present.
        $results = $this->patients->search(
            array_merge(['sort' => 'id_desc'], $request->all())
        );

        return view('patients.index', [
            'results'      => $results,
            'sexGenders'   => SexGender::options(),
            'patientTypes' => PatientType::options(),
        ]);
    }

    /**
     * Live patient search — returns up to 10 matching patients as JSON.
     * Searches by name (first_name / surname) or by exact ID when q is numeric.
     * Used by the diary quick-book Alpine.js autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        if ($q === '') {
            return response()->json([]);
        }

        $query = Patient::active()->limit(10);

        if (ctype_digit($q)) {
            $query->where('id', (int) $q);
        } else {
            $query->where(function ($q2) use ($q) {
                $q2->where('first_name', 'like', '%' . $q . '%')
                   ->orWhere('surname',    'like', '%' . $q . '%');
            });
        }

        $patients = $query->orderBy('surname')->orderBy('first_name')
            ->get(['id', 'first_name', 'surname', 'date_of_birth']);

        return response()->json(
            $patients->map(fn ($p) => [
                'id'            => $p->id,
                'first_name'    => $p->first_name,
                'surname'       => $p->surname,
                'date_of_birth' => $p->date_of_birth->format('d/m/Y'),
            ])
        );
    }

    /**
     * New Patient form.
     */
    public function create(): View
    {
        return view('patients.create', $this->formData());
    }

    /**
     * Store a new patient.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'               => ['required', 'string', 'max:20'],
            'first_name'          => ['required', 'string', 'max:255'],
            'surname'             => ['required', 'string', 'max:255'],
            'address_line_1'      => ['required', 'string', 'max:255'],
            'post_code'           => ['required', 'string', 'max:10'],
            'town_city'           => ['required', 'string', 'max:255'],
            'county'              => ['nullable', 'string', 'max:255'],
            'country'             => ['nullable', 'string', 'max:255'],
            'telephone_mobile'    => ['required', 'string', 'max:20'],
            'telephone_other'     => ['nullable', 'string', 'max:20'],
            'alt_contact_name'    => ['nullable', 'string', 'max:255'],
            'alt_tel_number'      => ['nullable', 'string', 'max:20'],
            'email'               => ['required', 'email', 'max:255'],
            'sex_gender'          => ['required', 'string'],
            'date_of_birth'       => ['required', 'date'],
            'practice_id'         => ['nullable', 'exists:practices,id'],
            'doctor_id'           => ['nullable', 'exists:doctors,id'],
            'doctor_other'        => ['nullable', 'string', 'max:255'],
            'has_glaucoma'               => ['boolean'],
            'is_diabetic'                => ['boolean'],
            'is_nhs'                     => ['boolean'],
            'patient_type'               => ['required', 'string'],
            'dropped_reason'             => ['nullable', 'string'],
            'how_heard'                  => ['nullable', 'string'],
            'how_heard_other'            => ['nullable', 'string', 'max:255'],
            'pct_id'                     => ['nullable', 'exists:pcts,id'],
            'domiciliary_reason'         => ['nullable', 'string'],
            'notes'                      => ['nullable', 'string'],
            // Medical
            'is_blind_partially_sighted' => ['boolean'],
            'has_hearing_impairment'     => ['boolean'],
            'has_retinitis_pigmentosa'   => ['boolean'],
            'physical_disabilities'      => ['nullable', 'string'],
            'mental_health_conditions'   => ['nullable', 'string'],
            // Social
            'in_full_time_education'     => ['boolean'],
            'benefits'                   => ['nullable', 'array'],
            'benefits.*'                 => ['string'],
            'next_of_kin_name'           => ['nullable', 'string', 'max:255'],
            'next_of_kin_relationship'   => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone'          => ['nullable', 'string', 'max:20'],
            'emergency_contact_name'     => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone'    => ['nullable', 'string', 'max:20'],
            'carer_name'                 => ['nullable', 'string', 'max:255'],
            'carer_phone'                => ['nullable', 'string', 'max:20'],
        ]);

        $patient = $this->patients->create($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Patient Information page — summary, details, and examination history.
     */
    public function show(Patient $patient, GosEligibilityService $gos): View
    {
        $patient->load(['practice', 'doctor', 'pct', 'documents.uploadedBy']);

        $examinations = $patient->examinations()
            ->with(['staff', 'refraction', 'lastEditedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Sync auto-calculated eligibility (never overwrites admin_override).
        // GOS18 is manual-only — is_eligible is always false; admin_override drives effective eligibility.
        foreach (['GOS1'  => $gos->isEligibleGos1($patient),
                  'GOS3'  => $gos->isEligibleGos3($patient),
                  'GOS6'  => $gos->isEligibleGos6($patient),
                  'GOS18' => false] as $formType => $eligible) {
            PatientGosForm::updateOrCreate(
                ['patient_id' => $patient->id, 'form_type' => $formType],
                ['is_eligible' => $eligible]
            );
        }

        $gosforms = $patient->gosforms()->orderBy('form_type')->get()->keyBy('form_type');

        return view('patients.show', compact('patient', 'examinations', 'gosforms'));
    }

    /**
     * Edit Patient Details form.
     */
    public function edit(Patient $patient): View
    {
        return view('patients.edit', array_merge(
            ['patient' => $patient],
            $this->formData(),
        ));
    }

    /**
     * Update patient details.
     */
    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'title'               => ['required', 'string', 'max:20'],
            'first_name'          => ['required', 'string', 'max:255'],
            'surname'             => ['required', 'string', 'max:255'],
            'address_line_1'      => ['required', 'string', 'max:255'],
            'post_code'           => ['required', 'string', 'max:10'],
            'town_city'           => ['required', 'string', 'max:255'],
            'county'              => ['nullable', 'string', 'max:255'],
            'country'             => ['nullable', 'string', 'max:255'],
            'telephone_mobile'    => ['required', 'string', 'max:20'],
            'telephone_other'     => ['nullable', 'string', 'max:20'],
            'alt_contact_name'    => ['nullable', 'string', 'max:255'],
            'alt_tel_number'      => ['nullable', 'string', 'max:20'],
            'email'               => ['required', 'email', 'max:255'],
            'sex_gender'          => ['required', 'string'],
            'date_of_birth'       => ['required', 'date'],
            'practice_id'         => ['nullable', 'exists:practices,id'],
            'doctor_id'           => ['nullable', 'exists:doctors,id'],
            'doctor_other'        => ['nullable', 'string', 'max:255'],
            'has_glaucoma'               => ['boolean'],
            'is_diabetic'                => ['boolean'],
            'is_nhs'                     => ['boolean'],
            'patient_type'               => ['required', 'string'],
            'dropped_reason'             => ['nullable', 'string'],
            'how_heard'                  => ['nullable', 'string'],
            'how_heard_other'            => ['nullable', 'string', 'max:255'],
            'pct_id'                     => ['nullable', 'exists:pcts,id'],
            'domiciliary_reason'         => ['nullable', 'string'],
            'notes'                      => ['nullable', 'string'],
            // Medical
            'is_blind_partially_sighted' => ['boolean'],
            'has_hearing_impairment'     => ['boolean'],
            'has_retinitis_pigmentosa'   => ['boolean'],
            'physical_disabilities'      => ['nullable', 'string'],
            'mental_health_conditions'   => ['nullable', 'string'],
            // Social
            'in_full_time_education'     => ['boolean'],
            'benefits'                   => ['nullable', 'array'],
            'benefits.*'                 => ['string'],
            'next_of_kin_name'           => ['nullable', 'string', 'max:255'],
            'next_of_kin_relationship'   => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone'          => ['nullable', 'string', 'max:20'],
            'emergency_contact_name'     => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone'    => ['nullable', 'string', 'max:20'],
            'carer_name'                 => ['nullable', 'string', 'max:255'],
            'carer_phone'                => ['nullable', 'string', 'max:20'],
        ]);

        $this->patients->update($patient, $validated);
        $patient->refresh();

        $gos = app(GosEligibilityService::class);
        foreach (['GOS1'  => $gos->isEligibleGos1($patient),
                  'GOS3'  => $gos->isEligibleGos3($patient),
                  'GOS6'  => $gos->isEligibleGos6($patient),
                  'GOS18' => false] as $formType => $eligible) {
            PatientGosForm::updateOrCreate(
                ['patient_id' => $patient->id, 'form_type' => $formType],
                ['is_eligible' => $eligible]
            );
        }

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Delete a patient and all their related records.
     *
     * Blocked if the patient has any signed examinations.
     * Explicitly deletes examinations (cascades to child tables) and appointments
     * before removing the patient, for SQLite FK compatibility.
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        if ($patient->examinations()->whereNotNull('signed_at')->exists()) {
            return redirect()->route('patients.show', $patient)
                ->with('error', 'Cannot delete — this patient has signed examination records.');
        }

        // Delete examinations first (exam child rows cascade via DB FK).
        // Then appointments. Then the patient (gos_forms cascade via DB FK).
        $patient->examinations()->delete();
        $patient->appointments()->delete();
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    // -------------------------------------------------------------------------

    /**
     * Shared view data for create and edit forms.
     */
    private function formData(): array
    {
        return [
            'titles'             => PatientTitle::options(),
            'practices'          => Practice::orderBy('name')->pluck('name', 'id'),
            'doctors'            => Doctor::orderBy('name')->pluck('name', 'id'),
            'pcts'               => Pct::orderBy('name')->pluck('name', 'id'),
            'sexGenders'         => SexGender::options(),
            'patientTypes'       => PatientType::options(),
            'droppedReasons'     => DroppedReason::options(),
            'howHeardOptions'    => HowHeard::options(),
            'domiciliaryReasons' => DomiciliaryReason::options(),
        ];
    }
}
