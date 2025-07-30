<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SiteSettingsController extends Controller
{
    /**
     * Show the site settings form
     */
    public function index()
    {
        $settings = SiteSettings::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the site settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg|max:1024',
            'primary_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'primary_dark_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'primary_light_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_dark_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_light_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'bg_dark_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'bg_medium_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'bg_light_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'success_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'warning_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'error_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'text_light_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'text_dark_color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        $settings = SiteSettings::getSettings();

        $data = $request->except(['site_logo', 'favicon']);

        // Handle logo upload if provided
        if ($request->hasFile('site_logo')) {
            // Delete old logo if it exists
            if ($settings->site_logo && Storage::exists('public/' . $settings->site_logo)) {
                Storage::delete('public/' . $settings->site_logo);
            }

            $logoPath = $request->file('site_logo')->store('logos', 'public');
            $data['site_logo'] = $logoPath;
        }

        // Handle favicon upload if provided
        if ($request->hasFile('favicon')) {
            // Delete old favicon if it exists
            if ($settings->favicon && Storage::exists('public/' . $settings->favicon)) {
                Storage::delete('public/' . $settings->favicon);
            }

            $faviconPath = $request->file('favicon')->store('favicons', 'public');
            $data['favicon'] = $faviconPath;
        }

        $settings->update($data);

        $this->updateCssVariables($settings);

        return redirect()->route('admin.settings.index')->with('success', 'Site settings updated successfully.');
    }

    /**
     * Reset site settings to default values
     */
    public function reset(Request $request)
    {
        $settings = SiteSettings::first();

        if ($settings) {
            // Directly update with hardcoded default values from migration
            $settings->update([
                'site_name' => 'ShopBot',
                'site_logo' => null,
                'favicon' => null,
                'primary_color' => '#5a4fcf',
                'primary_dark_color' => '#4a3fb8',
                'primary_light_color' => '#7b71e3',
                'secondary_color' => '#1e293b',
                'secondary_dark_color' => '#0f172a',
                'secondary_light_color' => '#334155',
                'accent_color' => '#f97316',
                'bg_dark_color' => '#111827',
                'bg_medium_color' => '#1f2937',
                'bg_light_color' => '#374151',
                'success_color' => '#10b981',
                'warning_color' => '#f59e0b',
                'error_color' => '#ef4444',
                'text_light_color' => '#f6f8fd',
                'text_dark_color' => '#111827',
                'is_default' => true,
            ]);
        }

        // Delete custom logo if exists
        if ($settings->site_logo && Storage::exists('public/' . $settings->site_logo)) {
            Storage::delete('public/' . $settings->site_logo);
            $settings->update(['site_logo' => null]);
        }

        // Delete custom favicon if exists
        if ($settings->favicon && Storage::exists('public/' . $settings->favicon)) {
            Storage::delete('public/' . $settings->favicon);
            $settings->update(['favicon' => null]);
        }

        $this->updateCssVariables($settings);

        return redirect()->route('admin.settings.index')->with('success', 'Site settings reset to default values.');
    }

    /**
     * Update CSS variables in the root.blade.php file
     */
    private function updateCssVariables($settings)
    {
        $cssPath = resource_path('views/layouts/css/root.blade.php');

        if (File::exists($cssPath)) {
            // Get the current CSS content
            $cssContent = File::get($cssPath);

            // Update color values in CSS
            $cssContent = preg_replace('/--primary: #[a-fA-F0-9]{6};/', '--primary: ' . $settings->primary_color . ';', $cssContent);
            $cssContent = preg_replace('/--primary-dark: #[a-fA-F0-9]{6};/', '--primary-dark: ' . $settings->primary_dark_color . ';', $cssContent);
            $cssContent = preg_replace('/--primary-light: #[a-fA-F0-9]{6};/', '--primary-light: ' . $settings->primary_light_color . ';', $cssContent);

            $cssContent = preg_replace('/--secondary: #[a-fA-F0-9]{6};/', '--secondary: ' . $settings->secondary_color . ';', $cssContent);
            $cssContent = preg_replace('/--secondary-dark: #[a-fA-F0-9]{6};/', '--secondary-dark: ' . $settings->secondary_dark_color . ';', $cssContent);
            $cssContent = preg_replace('/--secondary-light: #[a-fA-F0-9]{6};/', '--secondary-light: ' . $settings->secondary_light_color . ';', $cssContent);

            $cssContent = preg_replace('/--accent: #[a-fA-F0-9]{6};/', '--accent: ' . $settings->accent_color . ';', $cssContent);

            $cssContent = preg_replace('/--bg-dark: #[a-fA-F0-9]{6};/', '--bg-dark: ' . $settings->bg_dark_color . ';', $cssContent);
            $cssContent = preg_replace('/--bg-medium: #[a-fA-F0-9]{6};/', '--bg-medium: ' . $settings->bg_medium_color . ';', $cssContent);
            $cssContent = preg_replace('/--bg-light: #[a-fA-F0-9]{6};/', '--bg-light: ' . $settings->bg_light_color . ';', $cssContent);

            $cssContent = preg_replace('/--success: #[a-fA-F0-9]{6};/', '--success: ' . $settings->success_color . ';', $cssContent);
            $cssContent = preg_replace('/--warning: #[a-fA-F0-9]{6};/', '--warning: ' . $settings->warning_color . ';', $cssContent);
            $cssContent = preg_replace('/--error: #[a-fA-F0-9]{6};/', '--error: ' . $settings->error_color . ';', $cssContent);

            // These patterns might be missing from initial regex, add text light and dark colors
            $cssContent = preg_replace('/--text-light: #[a-fA-F0-9]{6};/', '--text-light: ' . $settings->text_light_color . ';', $cssContent);
            $cssContent = preg_replace('/--text-dark: #[a-fA-F0-9]{6};/', '--text-dark: ' . $settings->text_dark_color . ';', $cssContent);

            // Also update dependent variables that reference the main colors
            $cssContent = preg_replace('/--welcome-button-primary-bg: var\((.*?)\);/', '--welcome-button-primary-bg: var(--primary);', $cssContent);
            $cssContent = preg_replace('/--welcome-button-primary-hover: var\((.*?)\);/', '--welcome-button-primary-hover: var(--primary-dark);', $cssContent);
            $cssContent = preg_replace('/--app-link: var\((.*?)\);/', '--app-link: var(--primary);', $cssContent);
            $cssContent = preg_replace('/--app-link-hover: var\((.*?)\);/', '--app-link-hover: var(--primary-light);', $cssContent);

            // Save the updated CSS file
            File::put($cssPath, $cssContent);
        }
    }
}
