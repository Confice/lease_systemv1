<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccountController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        return view('admins.profile.index', [
            'user' => $user,
        ]);
    }

    public function settings()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        return view('admins.settings.index');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'firstName' => 'required|string|max:50',
            'middleName' => 'nullable|string|max:50',
            'lastName' => 'required|string|max:50',
            'contactNo' => ['required', 'regex:/^(\d{4}-\d{3}-\d{4}|09\d{9})$/'],
            'homeAddress' => 'nullable|string|max:255',
            'birthDate' => 'nullable|date',
        ]);

        $user->update($data);

        return redirect()
            ->route('admins.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Lease Manager') {
            abort(403, 'Unauthorized');
        }

        return redirect()
            ->route('admins.settings')
            ->with('success', 'Settings updated.');
    }
}
