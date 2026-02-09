<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LandingContactController extends Controller
{
    /**
     * Handle contact form submission from the landing page.
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required'    => 'Please enter your name.',
            'email.required'   => 'Please enter your email.',
            'email.email'      => 'Please enter a valid email address.',
            'message.required' => 'Please enter a message.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the errors below.',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $messageText = $request->input('message');

        try {
            $to = config('mail.from.address');
            Mail::to($to)->send(new ContactFormSubmitted($name, $email, $messageText));
        } catch (\Throwable $e) {
            \Log::error('Landing contact form mail failed: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'We could not send your message right now. Please try again later.',
                ], 500);
            }
            return redirect()->back()->with('error', 'We could not send your message. Please try again later.')->withInput();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been sent. We will get back to you soon.',
            ]);
        }
        return redirect()->back()->with('success', 'Thank you! Your message has been sent.');
    }
}
