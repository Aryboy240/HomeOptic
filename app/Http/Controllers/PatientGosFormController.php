<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientGosForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientGosFormController extends Controller
{
    public function showForm(Patient $patient, string $formType): View
    {
        abort_unless(in_array($formType, ['GOS1', 'GOS3', 'GOS6']), 404);
        $patient->load(['practice', 'doctor']);
        return view('gos.' . strtolower($formType), compact('patient'));
    }

    public function update(Request $request, Patient $patient, string $formType): RedirectResponse
    {
        $validated = $request->validate([
            'admin_override' => ['required', 'boolean'],
            'override_note'  => ['nullable', 'string', 'max:500'],
        ]);

        PatientGosForm::updateOrCreate(
            ['patient_id' => $patient->id, 'form_type' => $formType],
            [
                'admin_override' => $validated['admin_override'],
                'override_note'  => $validated['override_note'] ?? null,
            ]
        );

        return redirect()->route('patients.show', $patient)
            ->with('success', "{$formType} eligibility updated.");
    }

    public function clearOverride(Patient $patient, string $formType): RedirectResponse
    {
        PatientGosForm::where('patient_id', $patient->id)
            ->where('form_type', $formType)
            ->update(['admin_override' => null, 'override_note' => null]);

        return redirect()->route('patients.show', $patient)
            ->with('success', "{$formType} override cleared.");
    }
}
