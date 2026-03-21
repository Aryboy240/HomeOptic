<?php

namespace App\Factories;

use App\Enums\ExamType;
use App\Models\Examination;
use App\Models\ExamHistorySymptom;
use App\Models\ExamInvestigative;
use App\Models\ExamOphthalmoscopy;
use App\Models\ExamRefraction;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ExaminationFactory
{
    /**
     * Create a new examination with all four blank tab records atomically.
     *
     * Only the three pieces of information known at creation time are accepted.
     * All clinical content (GOS eligibility, Rx measurements, etc.) is filled
     * through the tab forms and saved separately after creation.
     *
     * @throws InvalidArgumentException if the exam type is not yet implemented.
     */
    public function create(Patient $patient, ExamType $type, User $staff): Examination
    {
        if (!$type->isSupported()) {
            throw new InvalidArgumentException(
                "Exam type [{$type->label()}] is not supported in the current version."
            );
        }

        return DB::transaction(function () use ($patient, $type, $staff) {
            $examination = Examination::create([
                'patient_id'  => $patient->id,
                'exam_type'   => $type,
                'examined_at' => today(),
                'staff_id'    => $staff->id,
            ]);

            // ── Carry-forward from previous signed exam ───────────────────────────
            // If the patient has a prior signed exam of the same type, seed certain
            // stable fields into the new blank tabs so clinicians do not have to
            // re-enter standing information on every visit.  We deliberately exclude:
            //   • reason_for_visit / last_exam fields — always clinically fresh
            //   • other_notes — context-specific to the previous visit
            //   • all ophthalmoscopy findings — must be observed anew each time
            //   • all refraction/Rx data — prescription changes visit-to-visit
            //   • signature fields — must never be pre-populated
            $prev = $patient
                ->examinations()
                ->where('exam_type', $type)
                ->whereNotNull('signed_at')
                ->orderByDesc('signed_at')
                ->with(['historySymptoms', 'investigative'])
                ->first();

            // History & Symptoms: carry forward standing medical/social history and
            // GOS eligibility context — things unlikely to change between visits.
            $historyData = ['examination_id' => $examination->id];
            if ($prev?->historySymptoms) {
                $hs = $prev->historySymptoms;
                $historyData += array_filter([
                    'gos_eligibility'        => $hs->gos_eligibility,
                    'gos_establishment_name' => $hs->gos_establishment_name,
                    'gos_establishment_town' => $hs->gos_establishment_town,
                    'medication_notes'       => $hs->medication_notes,
                    'medications'            => $hs->medications,
                    'has_glaucoma'           => $hs->has_glaucoma,
                    'has_fhg'                => $hs->has_fhg,
                    'is_diabetic'            => $hs->is_diabetic,
                    'poh'                    => $hs->poh,
                    'gh'                     => $hs->gh,
                    'fh'                     => $hs->fh,
                    'foh'                    => $hs->foh,
                ], fn ($v) => $v !== null && $v !== '' && $v !== []);
            }

            // Investigative: carry forward preferred tonometry method and colour
            // vision result — stable equipment/patient characteristics.
            $investigativeData = ['examination_id' => $examination->id];
            if ($prev?->investigative) {
                $inv = $prev->investigative;
                $investigativeData += array_filter([
                    'pre_iop_method'  => $inv->pre_iop_method?->value,
                    'post_iop_method' => $inv->post_iop_method?->value,
                    'colour_vision'   => $inv->colour_vision,
                ], fn ($v) => $v !== null && $v !== '');
            }
            // ─────────────────────────────────────────────────────────────────────

            // Four child rows — one per tab. Content is populated via form.
            // History and Investigative rows are pre-seeded from the carry-forward
            // data above (falls back to bare ['examination_id'] when no prior exam).
            ExamHistorySymptom::create($historyData);
            ExamOphthalmoscopy::create(['examination_id' => $examination->id]);
            ExamInvestigative::create($investigativeData);
            ExamRefraction::create(['examination_id'     => $examination->id]);

            // Return the parent with all tabs loaded so the caller has a
            // fully-hydrated record without needing a second query.
            return $examination->load([
                'historySymptoms',
                'ophthalmoscopy',
                'investigative',
                'refraction',
            ]);
        });
    }
}
