<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSettings extends Model
{
    protected $fillable = [
        'site_name',
        'site_logo',
        'favicon',
        'primary_color',
        'primary_dark_color',
        'primary_light_color',
        'secondary_color',
        'secondary_dark_color',
        'secondary_light_color',
        'accent_color',
        'bg_dark_color',
        'bg_medium_color',
        'bg_light_color',
        'success_color',
        'warning_color',
        'error_color',
        'text_light_color',
        'text_dark_color',
        'is_default',
    ];

    /**
     * Get the active site settings or create default ones
     */
    public static function getSettings()
    {
        $settings = static::first();

        if (!$settings) {
            $settings = static::create([
                'site_name' => 'ShopBot',
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

        return $settings;
    }

    /**
     * Reset settings to default values
     */
    public static function resetToDefault()
    {
        $settings = static::first();

        if ($settings) {
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

        return $settings;
    }

    /**
     * Get the favicon URL
     *
     * @return string
     */
    public function getFaviconUrl()
    {
        if ($this->favicon) {
            return Storage::url($this->favicon);
        }

        // Default to the hosted favicon image URL
        return 'https://cdn-icons-png.flaticon.com/512/2040/2040653.png';
    }
}
