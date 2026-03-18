<?php

namespace App\Http\Controllers;

use App\Contracts\PatientRepositoryInterface;
use App\Enums\DomiciliaryReason;
use App\Enums\DroppedReason;
use App\Enums\HowHeard;
use App\Enums\PatientType;
use App\Enums\SexGender;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Pct;
use App\Models\Practice;
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
        $results = null;

        if ($request->hasAny(['first_name', 'surname', 'patient_id', 'date_of_birth',
            'post_code', 'sex_gender', 'patient_type', 'has_glaucoma', 'is_diabetic'])) {
            $results = $this->patients->search($request->all());
        }

        return view('patients.index', [
            'results'      => $results,
            'sexGenders'   => SexGender::options(),
            'patientTypes' => PatientType::options(),
        ]);
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
            'telephone_mobile'    => ['nullable', 'string', 'max:20'],
            'telephone_other'     => ['nullable', 'string', 'max:20'],
            'alt_contact_name'    => ['nullable', 'string', 'max:255'],
            'alt_tel_number'      => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'max:255'],
            'sex_gender'          => ['required', 'string'],
            'date_of_birth'       => ['required', 'date'],
            'practice_id'         => ['nullable', 'exists:practices,id'],
            'doctor_id'           => ['nullable', 'exists:doctors,id'],
            'doctor_other'        => ['nullable', 'string', 'max:255'],
            'has_glaucoma'        => ['boolean'],
            'is_diabetic'         => ['boolean'],
            'is_nhs'              => ['boolean'],
            'patient_type'        => ['required', 'string'],
            'dropped_reason'      => ['nullable', 'string'],
            'how_heard'           => ['nullable', 'string'],
            'how_heard_other'     => ['nullable', 'string', 'max:255'],
            'pct_id'              => ['nullable', 'exists:pcts,id'],
            'domiciliary_reason'  => ['nullable', 'string'],
            'notes'               => ['nullable', 'string'],
        ]);

        $patient = $this->patients->create($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Patient Information page — summary, details, and examination history.
     */
    public function show(Patient $patient): View
    {
        $patient->load(['practice', 'doctor', 'pct']);
        $examinations = $patient->examinations()
            ->with('staff')
            ->orderBy('examined_at', 'desc')
            ->get();

        return view('patients.show', compact('patient', 'examinations'));
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
            'telephone_mobile'    => ['nullable', 'string', 'max:20'],
            'telephone_other'     => ['nullable', 'string', 'max:20'],
            'alt_contact_name'    => ['nullable', 'string', 'max:255'],
            'alt_tel_number'      => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'max:255'],
            'sex_gender'          => ['required', 'string'],
            'date_of_birth'       => ['required', 'date'],
            'practice_id'         => ['nullable', 'exists:practices,id'],
            'doctor_id'           => ['nullable', 'exists:doctors,id'],
            'doctor_other'        => ['nullable', 'string', 'max:255'],
            'has_glaucoma'        => ['boolean'],
            'is_diabetic'         => ['boolean'],
            'is_nhs'              => ['boolean'],
            'patient_type'        => ['required', 'string'],
            'dropped_reason'      => ['nullable', 'string'],
            'how_heard'           => ['nullable', 'string'],
            'how_heard_other'     => ['nullable', 'string', 'max:255'],
            'pct_id'              => ['nullable', 'exists:pcts,id'],
            'domiciliary_reason'  => ['nullable', 'string'],
            'notes'               => ['nullable', 'string'],
        ]);

        $this->patients->update($patient, $validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    // -------------------------------------------------------------------------

    /**
     * Shared view data for create and edit forms.
     */
    private function formData(): array
    {
        return [
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
