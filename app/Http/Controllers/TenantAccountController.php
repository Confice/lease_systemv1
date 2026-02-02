<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantAccountController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Unauthorized');
        }

        return view('tenants.profile.index', [
            'user' => $user,
        ]);
    }

    public function settings()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Unauthorized');
        }

        return view('tenants.settings.index');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'firstName' => 'required|string|max:50',
            'middleName' => 'nullable|string|max:50',
            'lastName' => 'required|string|max:50',
            'contactNo' => ['required', 'regex:/^09\d{9}$/'],
            'homeAddress' => 'required|string|max:255',
            'birthDate' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $age = \Carbon\Carbon::parse($value)->diffInYears(now());
                    if ($age < 18) {
                        $fail('You must be at least 18 years old.');
                    }
                }
            ],
        ]);

        $user->update($data);

        return redirect()
            ->route('tenants.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Tenant') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'themePreference' => 'required|in:light,dark',
            'reduceMotion' => 'sometimes|boolean',
        ]);

        $user->update([
            'themePreference' => $data['themePreference'],
            'reduceMotion' => $request->boolean('reduceMotion'),
        ]);

        return redirect()
            ->route('tenants.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
