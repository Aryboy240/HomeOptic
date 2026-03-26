<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\PendingBooking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search       = $request->input('search', '');
        $sort         = $request->input('sort', 'latest');
        $createdFrom  = $request->input('created_from', '');
        $createdTo    = $request->input('created_to', '');

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($createdFrom) && strtotime($createdFrom)) {
            $query->whereDate('created_at', '>=', $createdFrom);
        }

        if (!empty($createdTo) && strtotime($createdTo)) {
            $query->whereDate('created_at', '<=', $createdTo);
        }

        $query->orderBy(
            match($sort) {
                'oldest'   => 'created_at',
                'name_asc' => 'name',
                'name_desc'=> 'name',
                default    => 'created_at',
            },
            match($sort) {
                'oldest'   => 'asc',
                'name_asc' => 'asc',
                'name_desc'=> 'desc',
                default    => 'desc',
            }
        );

        $users = $query->get();

        $usersWithRecords = $this->userIdsWithClinicalRecords();

        $filters = compact('search', 'sort', 'createdFrom', 'createdTo');

        return view('admin.users.index', compact('users', 'usersWithRecords', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$validated['name']} created successfully.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if ($this->hasClinicalRecords($user)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'This user cannot be deleted because they have clinical records attached to their account. Deleting staff with clinical history would compromise the audit trail.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$name} deleted.");
    }

    private function hasClinicalRecords(User $user): bool
    {
        return Examination::where('staff_id', $user->id)->exists()
            || Examination::where('signed_by', $user->id)->exists()
            || Examination::where('last_edited_by', $user->id)->exists()
            || PendingBooking::where('admin_decided_by', $user->id)->exists();
    }

    private function userIdsWithClinicalRecords(): array
    {
        return Collection::make(
            Examination::whereNotNull('staff_id')->pluck('staff_id')
                ->merge(Examination::whereNotNull('signed_by')->pluck('signed_by'))
                ->merge(Examination::whereNotNull('last_edited_by')->pluck('last_edited_by'))
                ->merge(PendingBooking::whereNotNull('admin_decided_by')->pluck('admin_decided_by'))
        )->unique()->values()->all();
    }
}
