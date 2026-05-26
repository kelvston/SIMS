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
            'backup_enabled' => 'nullable|boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'backup_time' => 'required|date_format:H:i',
            'backup_retention' => 'required|integer|min:1|max:365',
            'backup_save_path' => 'nullable|string|max:1024',
        ]);

        $backupSavePath = $this->normalizeBackupPath($validated['backup_save_path'] ?? '');
        if ($request->boolean('backup_enabled') && $backupSavePath !== '' && ! str_starts_with($backupSavePath, '/')) {
            return back()
                ->withInput()
                ->with('error', 'Backup save location must be a full server path, for example /home/yoga/Desktop/test.');
        }

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
        Setting::updateOrCreate(['key' => 'backup_enabled'], ['value' => $request->boolean('backup_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'backup_frequency'], ['value' => $validated['backup_frequency']]);
        Setting::updateOrCreate(['key' => 'backup_time'], ['value' => $validated['backup_time']]);
        Setting::updateOrCreate(['key' => 'backup_retention'], ['value' => (string) $validated['backup_retention']]);
        Setting::updateOrCreate(['key' => 'backup_save_path'], ['value' => $backupSavePath]);

        return redirect()->route('settings.edit')->with('success', 'Organization settings updated successfully.');
    }

    private function normalizeBackupPath(?string $path): string
    {
        $path = trim((string) $path);

        if (str_starts_with($path, '~/')) {
            return rtrim((string) getenv('HOME'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . substr($path, 2);
        }

        return $path;
    }
}
