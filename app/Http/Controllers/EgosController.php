<?php

namespace App\Http\Controllers;

use App\Enums\GosSubmissionStatus;
use App\Models\GosSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EgosController extends Controller
{
    public function index(Request $request): View
    {
        $query = GosSubmission::with('patient')
            ->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('form_type')) {
            $query->where('form_type', $request->form_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('patient')) {
            $search = trim($request->patient);
            $query->whereHas('patient', function ($q) use ($search) {
                if (ctype_digit($search)) {
                    $q->where('id', (int) $search);
                } else {
                    $q->where(fn ($q2) =>
                        $q2->where('first_name', 'like', "%{$search}%")
                           ->orWhere('surname',    'like', "%{$search}%")
                    );
                }
            });
        }

        $submissions = $query->paginate(25)->withQueryString();

        return view('egos.index', [
            'submissions' => $submissions,
            'statuses'    => GosSubmissionStatus::options(),
            'formTypes'   => ['GOS1' => 'GOS1', 'GOS3' => 'GOS3', 'GOS6' => 'GOS6', 'GOS18' => 'GOS18'],
            'filters'     => $request->only(['date_from', 'date_to', 'form_type', 'status', 'patient']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id'    => ['required', 'integer', 'exists:patients,id'],
            'form_type'     => ['required', 'string', 'in:GOS1,GOS3,GOS6,GOS18'],
            'form_data'     => ['nullable', 'array'],
            'voucher_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $submission = GosSubmission::updateOrCreate(
            [
                'patient_id' => $validated['patient_id'],
                'form_type'  => $validated['form_type'],
            ],
            [
                'form_data'     => $validated['form_data'] ?? null,
                'voucher_value' => $validated['voucher_value'] ?? null,
            ]
        );

        return response()->json([
            'id'      => $submission->id,
            'created' => $submission->wasRecentlyCreated,
        ], $submission->wasRecentlyCreated ? 201 : 200);
    }

    public function updateStatus(Request $request, GosSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:unsubmitted,awaiting_confirmation,accepted,rejected'],
        ]);

        $newStatus = GosSubmissionStatus::from($validated['status']);

        $update = ['status' => $newStatus];

        if ($newStatus === GosSubmissionStatus::AwaitingConfirmation && ! $submission->submitted_at) {
            $update['submitted_at'] = now();
        }

        if ($newStatus === GosSubmissionStatus::Accepted && ! $submission->paid_at) {
            $update['paid_at'] = now();
        }

        $submission->update($update);

        return redirect()->back()->with('success', 'Submission status updated.');
    }

    public function batchSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:gos_submissions,id'],
        ]);

        $batchRef = 'BATCH-' . now()->format('Ymd') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        GosSubmission::whereIn('id', $validated['ids'])
            ->update([
                'status'          => GosSubmissionStatus::AwaitingConfirmation,
                'submitted_at'    => now(),
                'batch_reference' => $batchRef,
            ]);

        $count = count($validated['ids']);

        return redirect()->back()
            ->with('success', "{$count} submission(s) submitted with batch reference {$batchRef}.");
    }

    public function destroy(GosSubmission $submission): RedirectResponse
    {
        if ($submission->status !== GosSubmissionStatus::Unsubmitted) {
            return redirect()->back()->with('error', 'Cannot delete a submitted form.');
        }

        $submission->delete();

        return redirect()->route('egos.index')->with('success', 'Submission deleted.');
    }

    public function batchMarkPaid(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:gos_submissions,id'],
        ]);

        GosSubmission::whereIn('id', $validated['ids'])
            ->update([
                'status'  => GosSubmissionStatus::Accepted,
                'paid_at' => now(),
            ]);

        $count = count($validated['ids']);

        return redirect()->back()
            ->with('success', "{$count} submission(s) marked as accepted/paid.");
    }
}
