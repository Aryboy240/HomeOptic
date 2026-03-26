<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Examination;
use App\Models\PendingBooking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if (User::count() === 1) {
            return back()->withErrors(
                ['delete_blocked' => 'You cannot delete the only admin account.'],
                'userDeletion'
            );
        }

        $hasClinicalRecords = Examination::where('staff_id', $user->id)->exists()
            || Examination::where('signed_by', $user->id)->exists()
            || Examination::where('last_edited_by', $user->id)->exists()
            || PendingBooking::where('admin_decided_by', $user->id)->exists();

        if ($hasClinicalRecords) {
            return back()->withErrors(
                ['delete_blocked' => 'This user cannot be deleted because they have clinical records attached to their account. Deleting staff with clinical history would compromise the audit trail.'],
                'userDeletion'
            );
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
