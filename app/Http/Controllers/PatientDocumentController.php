<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientDocumentController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $request->validate([
            'file'        => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $uploaded  = $request->file('file');
        $original  = $uploaded->getClientOriginalName();
        $stored    = uniqid() . '_' . $original;
        $path      = $uploaded->storeAs(
            'patient-documents/' . $patient->id,
            $stored,
            'local'
        );

        $patient->documents()->create([
            'filename'    => $original,
            'stored_path' => $path,
            'description' => $request->input('description'),
            'uploaded_by' => $request->user()->id,
        ]);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Document uploaded successfully.');
    }

    public function download(PatientDocument $document): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($document->stored_path), 404);

        return Storage::disk('local')->download($document->stored_path, $document->filename);
    }

    public function destroy(PatientDocument $document): RedirectResponse
    {
        $patient = $document->patient_id;

        Storage::disk('local')->delete($document->stored_path);
        $document->delete();

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Document deleted.');
    }
}
