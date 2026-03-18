<?php

namespace App\Observers;

use App\Jobs\GenerateExaminationReportJob;
use App\Models\Examination;

class ExaminationObserver
{
    /**
     * Dispatch a PDF report job when an examination is signed off.
     *
     * Fires on update rather than creation because the examination record is
     * created blank when the form is opened — all clinical content is filled in
     * during the session and the record is only meaningful once the optometrist
     * clicks "Click To Sign".
     */
    public function updated(Examination $examination): void
    {
        if ($examination->wasChanged('signed_at') && $examination->signed_at !== null) {
            GenerateExaminationReportJob::dispatch($examination);
        }
    }
}
