<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display tenant-submitted feedback for lease managers.
     */
    public function adminIndex()
    {
        $feedbackEntries = Feedback::with(['tenant', 'contract.stall.marketplace'])
            ->whereNull('archived_at')
            ->latest('created_at')
            ->paginate(10);

        return view('admins.feedback_form.index', [
            'feedbackEntries' => $feedbackEntries,
        ]);
    }

    /**
     * Show a single feedback entry. Returns HTML for browser (e.g. "Go to" from activity),
     * JSON for AJAX (e.g. modal load).
     */
    public function show(Feedback $feedback)
    {
        $feedback->load(['tenant', 'contract.stall.marketplace']);

        $sections = [
            'Marketplace & Stall Experience' => [
                'usability_comprehension' => 'My stall was clean and ready for operations on move-in.',
                'usability_learning' => 'Maintenance requests for my stall are addressed promptly.',
                'usability_effort' => 'Common areas (restrooms, walkways, etc.) are well-maintained.',
                'usability_interface' => 'The marketplace environment feels safe and welcoming.',
            ],
            'Lease Operations & Support' => [
                'functionality_registration' => 'Store applications and renewals are easy to process.',
                'functionality_tasks' => 'Lease/marketplace staff respond to my concerns promptly.',
                'functionality_results' => 'Billing and charges are accurate and easy to understand.',
                'functionality_security' => 'Policies on security and crowd control are effective.',
            ],
            'System Experience' => [
                'reliability_error_handling' => 'I can accomplish my leasing tasks in the system without issues.',
                'reliability_command_tolerance' => 'System errors are handled clearly (helpful messages or guidance).',
                'reliability_recovery' => 'I can recover or retry easily if something fails online.',
            ],
        ];

        // Browser "Go to" from Recent Activity: return HTML page (not JSON)
        if (! request()->expectsJson()) {
            return view('admins.feedback_form.show', [
                'feedback' => $feedback,
                'sections' => $sections,
            ]);
        }

        $sectionDetails = [];
        foreach ($sections as $sectionName => $items) {
            $sectionDetails[$sectionName] = collect($items)->map(function ($label, $field) use ($feedback) {
                return [
                    'label' => $label,
                    'value' => $feedback->{$field},
                ];
            })->values();
        }

        return response()->json([
            'id' => $feedback->feedbackID,
            'submitted_at' => optional($feedback->created_at)->format('F d, Y h:i A'),
            'tenant' => [
                'name' => $feedback->tenant
                    ? trim(($feedback->tenant->firstName ?? '') . ' ' . ($feedback->tenant->lastName ?? ''))
                    : 'Unknown Tenant',
                'email' => $feedback->tenant->email ?? null,
                'contact' => $feedback->tenant->contactNo ?? null,
            ],
            'contract' => [
                'id' => $feedback->contractID,
                'start_date' => optional(optional($feedback->contract)->startDate)->format('M d, Y'),
                'end_date' => optional(optional($feedback->contract)->endDate)->format('M d, Y'),
            ],
            'stall' => [
                'stall_no' => optional(optional($feedback->contract)->stall)->stallNo,
                'marketplace' => optional(optional(optional($feedback->contract)->stall)->marketplace)->marketplace,
                'marketplace_address' => optional(optional(optional($feedback->contract)->stall)->marketplace)->marketplaceAddress ?? null,
            ],
            'sections' => $sectionDetails,
            'comments' => $feedback->comments,
        ]);
    }

    /**
     * Archive a specific feedback entry.
     */
    public function archive(Feedback $feedback)
    {
        $feedback->update([
            'archived_at' => now(),
        ]);

        try {
            \App\Services\ActivityLogService::logDelete('feedbacks', $feedback->feedbackID, "Archived feedback #{$feedback->feedbackID}");
        } catch (\Exception $e) {
            \Log::warning("Failed to log feedback archive activity: " . $e->getMessage());
        }

        return redirect()
            ->route('admins.feedback.index')
            ->with('success', 'Feedback archived successfully.');
    }

    /**
     * Show the feedback form for the tenant side.
     */
    public function tenantForm()
    {
        return view('tenants.feedback.index');
    }

    /**
     * Store feedback submitted from the tenant side.
     */
    public function tenantStore(Request $request)
    {
        $user = Auth::user();

        $activeContract = Contract::where('userID', $user->id)
            ->where('contractStatus', 'Active')
            ->whereNull('deleted_at')
            ->latest('startDate')
            ->first();

        if (!$activeContract) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'You need an active contract before submitting this survey.');
        }

        $validated = $request->validate([
            'usability_comprehension'      => 'required|integer|min:1|max:5',
            'usability_learning'           => 'required|integer|min:1|max:5',
            'usability_effort'             => 'required|integer|min:1|max:5',
            'usability_interface'          => 'required|integer|min:1|max:5',
            'functionality_registration'   => 'required|integer|min:1|max:5',
            'functionality_tasks'          => 'required|integer|min:1|max:5',
            'functionality_results'        => 'required|integer|min:1|max:5',
            'functionality_security'       => 'required|integer|min:1|max:5',
            'reliability_error_handling'   => 'required|integer|min:1|max:5',
            'reliability_command_tolerance'=> 'required|integer|min:1|max:5',
            'reliability_recovery'         => 'required|integer|min:1|max:5',
            'comments'                     => 'nullable|string|max:2000',
        ]);

        $feedback = Feedback::create(array_merge($validated, [
            'contractID' => $activeContract->contractID,
            'user_id'    => $user->id,
        ]));

        try {
            \App\Services\ActivityLogService::logCreate('feedbacks', $feedback->feedbackID, "Feedback submitted (contract #{$activeContract->contractID}).");
        } catch (\Exception $e) {
            \Log::warning("Failed to log feedback create: " . $e->getMessage());
        }

        return redirect()
            ->back()
            ->with('success', 'Thank you for your feedback.');
    }
}

