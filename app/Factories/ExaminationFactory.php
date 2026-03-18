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

            // Four blank child rows — one per tab. Content is populated via form.
            ExamHistorySymptom::create(['examination_id' => $examination->id]);
            ExamOphthalmoscopy::create(['examination_id' => $examination->id]);
            ExamInvestigative::create(['examination_id'  => $examination->id]);
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
