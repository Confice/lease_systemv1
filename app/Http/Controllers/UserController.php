<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return view('admins.users.index');
    }

    public function data()
    {
        $users = User::whereNull('deleted_at')->orderBy('id')->get();

        $data = $users->map(function ($u) {
            return [
                'id'        => $u->id,
                'firstName' => $u->firstName,
                'middleName'=> $u->middleName,
                'lastName'  => $u->lastName,
                'email'     => $u->email,
                'role'      => $u->role,
                'userStatus'=> $u->userStatus,
                'customReason' => $u->customReason,
                'created_at'=> optional($u->created_at)->format('Y-m-d'),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function print()
    {
        $users = User::whereNull('deleted_at')->orderBy('id')->get();
        return view('admins.print', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstName' => 'required|string|max:50',
                'middleName'=> 'nullable|string|max:50',
                'lastName'  => 'required|string|max:50',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->whereNull('deleted_at')
                ],
                'role'      => 'required|string',
                'userStatus'=> 'nullable|string',
                'contactNo' => [
                    'required',
                    'regex:/^09\d{9}$/'
                ],
                'homeAddress'=> 'required|string|max:255',
                'birthDate' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        $age = \Carbon\Carbon::parse($value)->diffInYears(now());
                        if ($age < 18) {
                            $fail('User must be 18 years old or above.');
                        }
                    }
                ],
            ]);

            DB::beginTransaction();

            // Create user with PENDING status, no password yet
            $user = User::create([
                'firstName'   => $validated['firstName'],
                'middleName'  => $validated['middleName'],
                'lastName'    => $validated['lastName'],
                'email'       => $validated['email'],
                'password'    => null, 
                'role'        => $validated['role'],
                'userStatus'  => 'Pending',
                'contactNo'   => $request->contactNo,
                'homeAddress' => $request->homeAddress,
                'birthDate'   => $request->birthDate,
            ]);
            
            // Generate verification token
            $token = Str::random(64);

            // Store token in existing password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );

            // Send activation email (don't fail if email fails)
            try {
                Mail::to($user->email)->send(new \App\Mail\NewUserActivation($user, $token));
            } catch (\Exception $e) {
                // Log email error but don't fail the user creation
                \Log::warning("Failed to send activation email to {$user->email}: " . $e->getMessage());
            }

            DB::commit();

            // Log activity
            try {
                \App\Services\ActivityLogService::logCreate('users', $user->id, "User created: {$user->email} ({$user->role})");
            } catch (\Exception $e) {
                \Log::warning("Failed to log user creation activity: " . $e->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'User added successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Failed to create user: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'firstName' => $user->firstName,
            'middleName' => $user->middleName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'role' => $user->role,
            'userStatus' => $user->userStatus,
            'customReason' => $user->customReason,
            'contactNo' => $user->contactNo,
            'homeAddress' => $user->homeAddress,
            'birthDate' => $user->birthDate,
            'created_at' => optional($user->created_at)->toDateTimeString(),
        ]);
    }

    public function edit(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $currentStatus = $user->userStatus;
        $newStatus = $request->input('userStatus');

        $allowedTransitions = [
            'Active' => ['Active', 'Deactivated'],
            'Pending' => ['Pending', 'Deactivated'],
            'Deactivated' => ['Active', 'Deactivated'],
        ];

        $data = $request->validate([
            'firstName'   => 'required|string|max:100',
            'middleName'  => 'nullable|string|max:100',
            'lastName'    => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($user->id)
            ],
            'role'        => [
                'required',
                Rule::in(['Lease Manager','Tenant'])
            ],
            'userStatus'  => [
                'required',
                Rule::in(['Active','Pending','Deactivated'])
            ],
            'customReason' => [
                Rule::requiredIf(fn() => $request->userStatus === 'Deactivated'),
                'nullable',
                'string',
                'max:500'
            ],
            'contactNo' => [
                'required',
                'regex:/^09\d{9}$/'
            ],
            'homeAddress' => 'required|string|max:255',
            'birthDate' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $age = \Carbon\Carbon::parse($value)->diffInYears(now());
                    if ($age < 18) {
                        $fail('User must be 18 years old or above.');
                    }
                }
            ],
        ]);

        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            return response()->json(['error' => "Invalid status transition"], 422);
        }

        // Only allow custom reason if status is Deactivated
        if ($data['userStatus'] !== 'Deactivated') {
            $data['customReason'] = null;
        }

        $user->update($data);

        // Log activity
        try {
            $statusChange = $currentStatus !== $newStatus ? " (Status: {$currentStatus} → {$newStatus})" : "";
            \App\Services\ActivityLogService::logUpdate('users', $user->id, "User updated: {$user->email}{$statusChange}");
        } catch (\Exception $e) {
            \Log::warning("Failed to log user update activity: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'User successfully updated!']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted.']);
    }

    public function archiveMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        $count = User::whereIn('id', $request->ids)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "{$count} user(s) archived successfully."
        ]);
    }

    public function exportCsv()
    {
        $fileName = 'users_' . now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID','First Name','Middle Name','Last Name','Email','Role','Status','Deactivation Reason','Created At']);

            User::whereNull('deleted_at')->orderBy('id')->chunk(200, function ($users) use ($handle) {
                foreach ($users as $u) {
                    fputcsv($handle, [
                        'USER-' . str_pad($u->id, 4, '0', STR_PAD_LEFT),
                        $u->firstName,
                        $u->middleName,
                        $u->lastName,
                        $u->email,
                        $u->role,
                        $u->userStatus,
                        $u->customReason ?? '-',
                        optional($u->created_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition','attachment; filename="'.$fileName.'"');

        return $response;
    }
}
