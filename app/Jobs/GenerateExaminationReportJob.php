<?php

namespace App\Jobs;

use App\Models\Examination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateExaminationReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Examination $examination)
    {
    }

    public function handle(): void
    {
        $examination = Examination::with([
            'patient.practice',
            'patient.doctor',
            'staff',
            'signedBy',
            'historySymptoms',
            'ophthalmoscopy',
            'investigative',
            'refraction',
        ])->findOrFail($this->examination->id);

        $pdf = Pdf::loadView('examinations.report', compact('examination'))
            ->setPaper('a4', 'portrait');

        $path = 'reports/examination-' . $examination->id . '.pdf';

        Storage::disk('local')->put($path, $pdf->output());

        $examination->update(['report_path' => $path]);
    }
}
