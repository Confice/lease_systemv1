<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerifyEmailMail;
use App\Mail\NewUserActivation;
use App\Services\ActivityLogService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Show register form
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Register user
    public function register(Request $request)
    {
        $request->validate([
            'firstName'   => 'required|string|max:50',
            'middleName'  => 'nullable|string|max:50',
            'lastName'    => 'required|string|max:50',
            'email'       => 'required|email|unique:users,email',
            'birthDate'   => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $age = \Carbon\Carbon::parse($value)->diffInYears(now());
                    if ($age < 18) {
                        $fail('You must be at least 18 years old to register.');
                    }
                }
            ],
            'contactNo'   => ['required','regex:/^09\d{9}$/'],
            'homeAddress' => 'required|string|max:255',
            'password'    => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'firstName'   => $request->firstName,
                'middleName'  => $request->middleName,
                'lastName'    => $request->lastName,
                'email'       => $request->email,
                'birthDate'   => $request->birthDate,
                'contactNo'   => $request->contactNo,
                'homeAddress' => $request->homeAddress,
                'password'    => Hash::make($request->password),
                'role'        => 'Tenant',
                'userStatus'  => 'Pending',
            ]);

            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );

            Mail::to($user->email)->send(new VerifyEmailMail($user, $token));

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Registration successful! Please check your email.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() // <-- show the actual reason
            ]);
        }
    }

    // Verify email
    public function verifyEmail($token)
        {
            $record = DB::table('password_reset_tokens')->where('token', $token)->first();

            // If token not found
            if (!$record) {
                return view('auth.link_invalid', ['message' => 'This activation link is invalid or already used.', 'hideBack' => true]);
            }

            // If expired (after 60 minutes)
            if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
                DB::table('password_reset_tokens')->where('email', $record->email)->delete();
                return view('auth.link_invalid', ['message' => 'This activation link has expired. Please register again.', 'hideBack' => true]);
            }

            // Verify user
            $user = User::where('email', $record->email)->first();

            if (!$user) {
                return view('auth.link_invalid', ['message' => 'User not found for this activation link.', 'hideBack' => true]);
            }

            // Already activated?
            if ($user->userStatus === 'Active' && $user->email_verified_at) {
                return view('auth.link_invalid', ['message' => 'This account is already activated. You can now log in.', 'hideBack' => true]);
            }

            // Activate account
            $user->update([
                'email_verified_at' => now(),
                'userStatus'        => 'Active'
            ]);

            DB::table('password_reset_tokens')->where('email', $record->email)->delete();

            return redirect()->route('login')->with('success', 'Your account has been activated! You can now login.');
        }

    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Show password setup form
    public function showSetupPasswordForm($token)
    {
        $record = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$record) {
            return view('auth.link_invalid', ['message' => 'This activation link is invalid or already used.']);
        }

        // Check expiration (1 hour)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $record->email)->delete();
            return view('auth.link_invalid', [
                'message' => 'This activation link has expired. Request a new one below.',
                'email' => $record->email,
                'resendRoute' => route('setup.password.resend'),
                'hideBack' => true,
            ]);
        }

        return view('auth.setup_password', ['token' => $token, 'email' => $record->email]);
    }

    // Handle password setup submission
    public function setupPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.'
        ]);

        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->token)
                    ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        $user = User::where('email', $request->email)->first();

        $user->update([
            'password' => Hash::make($request->password),
            'userStatus' => 'Active',
            'email_verified_at' => now(),
            'isFirstLogin' => false
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been set. You can now login.');
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and is deactivated first (before password check)
        if ($user && $user->userStatus === 'Deactivated') {
            return response()->json([
                'errors' => ['email' => ['This account is currently deactivated. For more information, please reach out to the system administrator.']]
            ], 422);
        }

        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['No account found with this email.']]
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => ['password' => ['Incorrect email or password.']]
            ], 422);
        }

        if ($user->userStatus === 'Pending') {
            return response()->json([
                'errors' => ['email' => ['Please activate your account using the verification email.']]
            ], 422);
        }

        if ($user->userStatus !== 'Active') {
            return response()->json([
                'errors' => ['email' => ['Your account is not active.']]
            ], 422);
        }

        Auth::login($user);

        // Log login activity
        try {
            ActivityLogService::logLogin($user->id);
        } catch (\Exception $e) {
            \Log::warning("Failed to log login activity: " . $e->getMessage());
        }

        $redirect = match($user->role) {
            'Tenant' => route('tenants.dashboard'),
            'Lease Manager' => route('admins.dashboard'),
            default => null
        };

        if (!$redirect) {
            Auth::logout();
            return response()->json([
                'errors' => ['email' => ['Unauthorized access.']]
            ], 422);
        }

        return response()->json(['success' => true, 'redirect' => $redirect]);
    }

    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    // Handle forgot password request
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        $resetUrl = url("/reset-password/{$token}");
        
        try {
            Mail::to($request->email)->send(new \App\Mail\ResetPassword($user, $resetUrl));
        } catch (\Exception $e) {
            Log::error('Failed to send reset email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'We could not send the reset email right now. Please try again later.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'We\'ve sent you an email with a new link. Please check your inbox.'
        ]);
    }

    // Show reset password form
    public function showResetPasswordForm($token)
    {
        $record = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$record) {
            return view('auth.link_invalid', ['message' => 'This reset link is invalid or already used.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return view('auth.link_invalid', [
                'message' => 'This reset link has expired. Request a new one below.',
                'email' => $record->email,
                'resendRoute' => route('password.email'),
                'hideBack' => false,
            ]);
        }

        return view('auth.reset_password', ['token' => $token, 'email' => $record->email]);
    }

    // Handle reset password submission
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset. You can now login.');
    }

    // Resend setup password link when initial link has expired
    public function resendSetupPasswordLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate a new token and timestamp
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        try {
            Mail::to($user->email)->send(new NewUserActivation($user, $token));
        } catch (\Exception $e) {
            Log::error('Failed to resend setup link: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'We could not send the setup link right now. Please try again later.'
                ], 500);
            }
            return back()->withErrors(['email' => 'We could not send the setup link right now. Please try again later.']);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'We\'ve sent you an email with a new link. Please check your inbox.'
            ]);
        }

        return redirect()->route('login')->with('success', 'A new setup password link has been emailed to you.');
    }

    // Admin-triggered password reset for a user
    public function sendResetPassword(User $user)
    {
        // Check if user can be reset
        if ($user->userStatus !== 'Active') {
            return response()->json([
                'success' => false,
                'message' => 'Password reset is only allowed for active accounts.'
            ], 422);
        }

        try {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );

            $resetUrl = url("/reset-password/{$token}");
            Mail::to($user->email)->send(new \App\Mail\ResetPassword($user, $resetUrl));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to '.$user->email
            ]);

        } catch (\Exception $e) {
            Log::error("Admin reset password failed for {$user->email}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link. Please try again later.'
            ], 500);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        $userId = Auth::id();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log logout activity
        if ($userId) {
            try {
                ActivityLogService::logLogout($userId);
            } catch (\Exception $e) {
                \Log::warning("Failed to log logout activity: " . $e->getMessage());
            }
        }

        return redirect()->route('login');
    }
}