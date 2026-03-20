<?php

namespace App\Http\Controllers;

use App\Contracts\ExaminationRepositoryInterface;
use App\Enums\ExamOutcome;
use App\Enums\ExamType;
use App\Enums\GosEligibility;
use App\Enums\PatientType;
use App\Enums\PrismDirection;
use App\Factories\ExaminationFactory;
use App\Models\Examination;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExaminationController extends Controller
{
    public function __construct(
        private readonly ExaminationRepositoryInterface $examinations,
        private readonly ExaminationFactory $factory,
    ) {
    }

    /**
     * Create a new examination for a patient and redirect to the exam form.
     * Only Spectacle exams are supported in the prototype.
     */
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $examination = $this->factory->create(
            $patient,
            ExamType::Spectacle,
            $request->user(),
        );

        return redirect()->route('examinations.show', $examination)
            ->with('success', 'Examination created. Complete the tabs below.');
    }

    /**
     * Tabbed examination form — all four tabs with current data.
     */
    public function show(Examination $examination): View
    {
        $examination = $this->examinations->find($examination->id);

        return view('examinations.show', [
            'examination'      => $examination,
            'gosEligibilities' => GosEligibility::options(),
            'prismDirections'  => PrismDirection::options(),
            'examOutcomes'     => ExamOutcome::options(),
            'patientTypes'     => PatientType::options(),
        ]);
    }

    /**
     * Save Tab 1 — History & Symptoms.
     */
    public function updateHistory(Request $request, Examination $examination): RedirectResponse
    {
        $validated = $request->validate([
            'gos_eligibility'         => ['required', 'string'],
            'gos_establishment_name'  => ['nullable', 'string', 'max:255'],
            'gos_establishment_town'  => ['nullable', 'string', 'max:255'],
            'last_exam_first'         => ['boolean'],
            'last_exam_not_known'     => ['boolean'],
            'last_exam_date'          => ['nullable', 'date'],
            'reason_for_visit'        => ['nullable', 'string'],
            'poh'                     => ['nullable', 'string'],
            'gh'                      => ['nullable', 'string'],
            'medication_notes'        => ['nullable', 'string'],
            'fh'                      => ['nullable', 'string'],
            'foh'                     => ['nullable', 'string'],
            'medications'             => ['nullable', 'array'],
            'medications.*'           => ['string'],
            'other_notes'             => ['nullable', 'string'],
            'has_glaucoma'            => ['boolean'],
            'has_fhg'                 => ['boolean'],
            'is_diabetic'             => ['boolean'],
        ]);

        $examination->historySymptoms->update($validated);

        return redirect()->route('examinations.show', $examination)
            ->with('success', 'History & Symptoms saved.');
    }

    /**
     * Save Tab 2 — Ophthalmoscopy & External Examination.
     */
    public function updateOphthalmoscopy(Request $request, Examination $examination): RedirectResponse
    {
        $validated = $request->validate([
            'ophthalmoscopy_notes'   => ['nullable', 'string'],
            'right_pupils'           => ['nullable', 'string', 'max:255'],
            'right_lids_lashes'      => ['nullable', 'string', 'max:255'],
            'right_lashes'           => ['nullable', 'string', 'max:255'],
            'right_conjunc'          => ['nullable', 'string', 'max:255'],
            'right_cornea'           => ['nullable', 'string', 'max:255'],
            'right_sclera'           => ['nullable', 'string', 'max:255'],
            'right_ant_ch'           => ['nullable', 'string', 'max:255'],
            'right_media'            => ['nullable', 'string', 'max:255'],
            'right_cd'               => ['nullable', 'string', 'max:255'],
            'right_av'               => ['nullable', 'string', 'max:255'],
            'right_fundus_periphery' => ['nullable', 'string', 'max:255'],
            'right_macular'          => ['nullable', 'string', 'max:255'],
            'right_ret_grading'      => ['nullable', 'string', 'max:255'],
            'left_pupils'            => ['nullable', 'string', 'max:255'],
            'left_lids_lashes'       => ['nullable', 'string', 'max:255'],
            'left_lashes'            => ['nullable', 'string', 'max:255'],
            'left_conjunc'           => ['nullable', 'string', 'max:255'],
            'left_cornea'            => ['nullable', 'string', 'max:255'],
            'left_sclera'            => ['nullable', 'string', 'max:255'],
            'left_ant_ch'            => ['nullable', 'string', 'max:255'],
            'left_media'             => ['nullable', 'string', 'max:255'],
            'left_cd'                => ['nullable', 'string', 'max:255'],
            'left_av'                => ['nullable', 'string', 'max:255'],
            'left_fundus_periphery'  => ['nullable', 'string', 'max:255'],
            'left_macular'           => ['nullable', 'string', 'max:255'],
            'left_ret_grading'       => ['nullable', 'string', 'max:255'],
        ]);

        $examination->ophthalmoscopy->update($validated);

        return redirect()->route('examinations.show', $examination)
            ->with('success', 'Ophthalmoscopy saved.');
    }

    /**
     * Save Tab 3 — Further Investigative Techniques.
     */
    public function updateInvestigative(Request $request, Examination $examination): RedirectResponse
    {
        $validated = $request->validate([
            'drops_used'                 => ['boolean'],
            'drops_detail_batch'         => ['nullable', 'string', 'max:255'],
            'drops_expiry'               => ['nullable', 'date'],
            'drops_more_info'            => ['nullable', 'string'],
            'pre_iop_r'                  => ['nullable', 'string', 'max:20'],
            'pre_iop_l'                  => ['nullable', 'string', 'max:20'],
            'pre_iop_time'               => ['nullable', 'string', 'max:8'],
            'post_iop_r'                 => ['nullable', 'string', 'max:20'],
            'post_iop_l'                 => ['nullable', 'string', 'max:20'],
            'post_iop_time'              => ['nullable', 'string', 'max:8'],
            'ct_with_rx'                 => ['nullable', 'string', 'max:255'],
            'ct_with_rx_near'            => ['nullable', 'string', 'max:255'],
            'ct_with_rx_near_notes'      => ['nullable', 'string'],
            'ct_without_rx'              => ['nullable', 'string', 'max:255'],
            'ct_without_rx_near'         => ['nullable', 'string', 'max:255'],
            'ct_without_rx_near_notes'   => ['nullable', 'string'],
            'omb_near_h'                 => ['nullable', 'string', 'max:255'],
            'omb_near_v'                 => ['nullable', 'string', 'max:255'],
            'visual_fields_r'            => ['nullable', 'string', 'max:255'],
            'visual_fields_l'            => ['nullable', 'string', 'max:255'],
            'motility'                   => ['nullable', 'string', 'max:255'],
            'amsler_r'                   => ['nullable', 'string', 'max:255'],
            'amsler_r_notes'             => ['nullable', 'string'],
            'amsler_l'                   => ['nullable', 'string', 'max:255'],
            'amsler_l_notes'             => ['nullable', 'string'],
            'omb_h'                      => ['nullable', 'string', 'max:255'],
            'omb_v'                      => ['nullable', 'string', 'max:255'],
            'keratometry_r'              => ['nullable', 'string', 'max:255'],
            'keratometry_l'              => ['nullable', 'string', 'max:255'],
            'npc'                        => ['nullable', 'string', 'max:255'],
            'stereopsis'                 => ['nullable', 'string', 'max:255'],
            'colour_vision'              => ['nullable', 'string', 'max:255'],
            'amplitude_of_accommodation' => ['nullable', 'string', 'max:255'],
        ]);

        $examination->investigative->update($validated);

        return redirect()->route('examinations.show', $examination)
            ->with('success', 'Investigative Techniques saved.');
    }

    /**
     * Save Tab 4 — Refraction (all Rx sections, outcome, and recommendations).
     */
    public function updateRefraction(Request $request, Examination $examination): RedirectResponse
    {
        $validated = $request->validate([
            // Current Rx — Right
            'current_r_sph'         => ['nullable', 'numeric'],
            'current_r_cyl'         => ['nullable', 'numeric'],
            'current_r_axis'        => ['nullable', 'integer', 'min:0', 'max:180'],
            'current_r_prism'       => ['nullable', 'numeric'],
            'current_r_prism_dir'   => ['nullable', 'string'],
            'current_r_add'         => ['nullable', 'numeric'],
            'current_r_va'          => ['nullable', 'string', 'max:20'],
            // Current Rx — Left
            'current_l_sph'         => ['nullable', 'numeric'],
            'current_l_cyl'         => ['nullable', 'numeric'],
            'current_l_axis'        => ['nullable', 'integer', 'min:0', 'max:180'],
            'current_l_prism'       => ['nullable', 'numeric'],
            'current_l_prism_dir'   => ['nullable', 'string'],
            'current_l_add'         => ['nullable', 'numeric'],
            'current_l_va'          => ['nullable', 'string', 'max:20'],
            // Current Rx — Additional
            'current_pd_r'          => ['nullable', 'numeric'],
            'current_pd_l'          => ['nullable', 'numeric'],
            'current_bvd'           => ['nullable', 'numeric'],
            'current_bin_bcva'      => ['nullable', 'string', 'max:20'],
            'current_comments'      => ['nullable', 'string'],
            // Previous Rx Other — Right
            'prev_other_r_sph'      => ['nullable', 'numeric'],
            'prev_other_r_cyl'      => ['nullable', 'numeric'],
            'prev_other_r_axis'     => ['nullable', 'integer', 'min:0', 'max:180'],
            'prev_other_r_prism'    => ['nullable', 'numeric'],
            'prev_other_r_prism_dir'=> ['nullable', 'string'],
            'prev_other_r_add'      => ['nullable', 'numeric'],
            'prev_other_r_va'       => ['nullable', 'string', 'max:20'],
            // Previous Rx Other — Left
            'prev_other_l_sph'      => ['nullable', 'numeric'],
            'prev_other_l_cyl'      => ['nullable', 'numeric'],
            'prev_other_l_axis'     => ['nullable', 'integer', 'min:0', 'max:180'],
            'prev_other_l_prism'    => ['nullable', 'numeric'],
            'prev_other_l_prism_dir'=> ['nullable', 'string'],
            'prev_other_l_add'      => ['nullable', 'numeric'],
            'prev_other_l_va'       => ['nullable', 'string', 'max:20'],
            // Retinoscopy
            'retino_r_value'        => ['nullable', 'string'],
            'retino_l_value'        => ['nullable', 'string'],
            // Subjective — Right
            'subj_r_uav'            => ['nullable', 'string', 'max:20'],
            'subj_r_sph'            => ['nullable', 'numeric'],
            'subj_r_cyl'            => ['nullable', 'numeric'],
            'subj_r_axis'           => ['nullable', 'integer', 'min:0', 'max:180'],
            'subj_r_prism'          => ['nullable', 'numeric'],
            'subj_r_prism_dir'      => ['nullable', 'string'],
            'subj_r_va'             => ['nullable', 'string', 'max:20'],
            'subj_r_near_add'       => ['nullable', 'numeric'],
            'subj_r_near_prism'     => ['nullable', 'numeric'],
            'subj_r_near_prism_dir' => ['nullable', 'string'],
            'subj_r_near_acuity'    => ['nullable', 'string', 'max:20'],
            'subj_r_int_add'        => ['nullable', 'numeric'],
            'subj_r_int_prism'      => ['nullable', 'numeric'],
            'subj_r_int_prism_dir'  => ['nullable', 'string'],
            'subj_r_int_acuity'     => ['nullable', 'string', 'max:20'],
            // Subjective — Left
            'subj_l_uav'            => ['nullable', 'string', 'max:20'],
            'subj_l_sph'            => ['nullable', 'numeric'],
            'subj_l_cyl'            => ['nullable', 'numeric'],
            'subj_l_axis'           => ['nullable', 'integer', 'min:0', 'max:180'],
            'subj_l_prism'          => ['nullable', 'numeric'],
            'subj_l_prism_dir'      => ['nullable', 'string'],
            'subj_l_va'             => ['nullable', 'string', 'max:20'],
            'subj_l_near_add'       => ['nullable', 'numeric'],
            'subj_l_near_prism'     => ['nullable', 'numeric'],
            'subj_l_near_prism_dir' => ['nullable', 'string'],
            'subj_l_near_acuity'    => ['nullable', 'string', 'max:20'],
            'subj_l_int_add'        => ['nullable', 'numeric'],
            'subj_l_int_prism'      => ['nullable', 'numeric'],
            'subj_l_int_prism_dir'  => ['nullable', 'string'],
            'subj_l_int_acuity'     => ['nullable', 'string', 'max:20'],
            // Subjective — Additional
            'subj_pd_r'             => ['nullable', 'numeric'],
            'subj_pd_l'             => ['nullable', 'numeric'],
            'subj_pd_combined'      => ['nullable', 'numeric'],
            'subj_bvd'              => ['nullable', 'numeric'],
            'subj_bin_bcva'         => ['nullable', 'string', 'max:20'],
            'subj_notes'            => ['nullable', 'string'],
            // Outcome & recommendations
            'outcome'               => ['nullable', 'string'],
            'rec_distance'          => ['boolean'],
            'rec_near'              => ['boolean'],
            'rec_intermediate'      => ['boolean'],
            'rec_high_index'        => ['boolean'],
            'rec_bifocals'          => ['boolean'],
            'rec_varifocals'        => ['boolean'],
            'rec_occupational'      => ['boolean'],
            'rec_min_sub'           => ['boolean'],
            'rec_photochromic'      => ['boolean'],
            'rec_hardcoat'          => ['boolean'],
            'rec_tint'              => ['boolean'],
            'rec_mar'               => ['boolean'],
            // NHS & retest
            'nhs_voucher_dist'      => ['nullable', 'string', 'max:255'],
            'nhs_voucher_near'      => ['nullable', 'string', 'max:255'],
            'examination_comment'   => ['nullable', 'string'],
            'retest_after'          => ['nullable', 'string', 'max:50'],
            'retest_patient_type'   => ['nullable', 'string', 'max:1'],
        ]);

        $examination->refraction->update($validated);

        return redirect()->route('examinations.show', $examination)
            ->with('success', 'Refraction saved.');
    }

    /**
     * Download the generated PDF report for a signed examination.
     */
    public function report(Examination $examination): StreamedResponse
    {
        abort_if(is_null($examination->report_path), 404, 'Report not yet generated.');
        abort_unless(Storage::disk('local')->exists($examination->report_path), 404, 'Report file not found.');

        $filename = 'examination-' . $examination->id . '-' . $examination->patient->surname . '.pdf';

        return Storage::disk('local')->download($examination->report_path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Delete an unsigned examination and its child tab records (cascade handles children).
     */
    public function destroy(Examination $examination): RedirectResponse
    {
        abort_unless(is_null($examination->signed_at), 403, 'Signed examinations cannot be deleted.');

        $patient = $examination->patient;
        $examination->delete();

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Examination deleted.');
    }

    /**
     * Sign off an examination — triggers ExaminationObserver to dispatch the PDF report job.
     */
    public function sign(Request $request, Examination $examination): RedirectResponse
    {
        $examination->update([
            'signed_by' => $request->user()->id,
            'signed_at' => now(),
        ]);

        return redirect()->route('examinations.show', $examination)
            ->with('success', 'Examination signed successfully.');
    }
}
