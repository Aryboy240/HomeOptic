<?php

namespace App\Jobs;

use App\Models\Examination;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateExaminationReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Examination $examination)
    {
    }

    public function handle(): void
    {
        // TODO: Implement PDF report generation.
        //
        // This job is intentionally stubbed. A full implementation requires:
        //   1. A PDF package — barryvdh/laravel-dompdf is recommended.
        //   2. A Blade report template covering all four examination tabs
        //      (History & Symptoms, Ophthalmoscopy, Investigative, Refraction).
        //   3. A storage strategy for generated files (local disk or S3),
        //      and likely a reference stored back on the examination record.
        //
        // The job is triggered correctly (on examination sign-off) so the
        // real implementation only needs to replace this log call with the
        // generation logic.

        Log::info('GenerateExaminationReportJob: PDF generation not yet implemented.', [
            'examination_id' => $this->examination->id,
            'patient_id'     => $this->examination->patient_id,
            'exam_type'      => $this->examination->exam_type,
            'examined_at'    => $this->examination->examined_at,
        ]);
    }
}
