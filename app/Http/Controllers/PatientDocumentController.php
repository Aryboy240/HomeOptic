<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientDocumentController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'file'        => ['required', 'file', 'mimes:pdf,jpeg,jpg,png,gif,webp', 'max:10240'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $uploaded  = $request->file('file');
        $original  = $uploaded->getClientOriginalName();
        $stored    = uniqid() . '_' . $original;
        $mime      = $uploaded->getMimeType();
        $fileType  = str_starts_with($mime, 'image/') ? 'image' : 'pdf';

        $path = $uploaded->storeAs(
            'patient-documents/' . $patient->id,
            $stored,
            'local'
        );

        $patient->documents()->create([
            'title'       => $request->input('title'),
            'filename'    => $original,
            'stored_path' => $path,
            'file_type'   => $fileType,
            'description' => $request->input('description'),
            'uploaded_by' => $request->user()->id,
        ]);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'File uploaded successfully.');
    }

    public function download(PatientDocument $document): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($document->stored_path), 404);

        return Storage::disk('local')->download($document->stored_path, $document->filename);
    }

    public function view(PatientDocument $document): Response
    {
        abort_unless(Storage::disk('local')->exists($document->stored_path), 404);
        abort_unless($document->file_type === 'image', 404);

        $contents = Storage::disk('local')->get($document->stored_path);
        $mime     = Storage::disk('local')->mimeType($document->stored_path);

        return response($contents, 200)->header('Content-Type', $mime);
    }

    public function destroy(PatientDocument $document): RedirectResponse
    {
        $patient = $document->patient_id;

        Storage::disk('local')->delete($document->stored_path);
        $document->delete();

        return redirect()->route('patients.show', $patient)
            ->with('success', 'File deleted.');
    }
}
