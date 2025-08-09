<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Show the form for editing the organization settings.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.edit', compact('settings'));
    }

    /**
     * Update the specified settings in storage, including a logo upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'organization_address' => 'nullable|string|max:255',
            'organization_phone' => 'nullable|string|max:255',
            'organization_email' => 'nullable|email|max:255',
            'organization_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // New validation for image file
        ]);

        // Handle the logo file upload
        if ($request->hasFile('organization_logo')) {
            // Get the current logo path to delete the old one later
            $oldLogoPath = Setting::where('key', 'organization_logo_path')->first()->value ?? null;

            // Store the new logo and get its path
            $logoPath = $request->file('organization_logo')->store('logos', 'public');

            // Save the new logo path to settings
            Setting::updateOrCreate(
                ['key' => 'organization_logo_path'],
                ['value' => $logoPath]
            );

            // Delete the old logo file from storage
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }
        }

        // Update other settings
        Setting::updateOrCreate(['key' => 'organization_name'], ['value' => $validated['organization_name']]);
        Setting::updateOrCreate(['key' => 'organization_address'], ['value' => $validated['organization_address']]);
        Setting::updateOrCreate(['key' => 'organization_phone'], ['value' => $validated['organization_phone']]);
        Setting::updateOrCreate(['key' => 'organization_email'], ['value' => $validated['organization_email']]);

        return redirect()->route('settings.edit')->with('success', 'Organization settings updated successfully.');
    }
}
