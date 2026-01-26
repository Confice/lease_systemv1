<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    public function create()
    {
        return view('admins.marketplaces.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'marketplace' => 'required|string|max:255',
                'marketplaceAddress' => 'required|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'facebookLink' => 'nullable|url|max:255',
                'telephoneNo' => 'nullable|string|max:50',
                'viberNo' => 'nullable|string|max:50',
            ]);

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('marketplace-logos', 'public');
            }

            Marketplace::create([
                'marketplace' => $validated['marketplace'],
                'marketplaceAddress' => $validated['marketplaceAddress'],
                'logoPath' => $logoPath,
                'facebookLink' => $validated['facebookLink'] ?? null,
                'telephoneNo' => $validated['telephoneNo'] ?? null,
                'viberNo' => $validated['viberNo'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Marketplace added successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Failed to create marketplace: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create marketplace: ' . $e->getMessage()
            ], 500);
        }
    }
}
