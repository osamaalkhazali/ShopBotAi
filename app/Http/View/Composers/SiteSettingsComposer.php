<?php

namespace App\Http\View\Composers;

use App\Models\SiteSettings;
use Illuminate\View\View;

class SiteSettingsComposer
{
  /**
   * The site settings instance.
   *
   * @var \App\Models\SiteSettings
   */
  protected $siteSettings;

  /**
   * Create a new site settings composer.
   *
   * @return void
   */
  public function __construct()
  {
    // Share site settings across all views
    $this->siteSettings = SiteSettings::getSettings();
  }

  /**
   * Bind data to the view.
   *
   * @param  \Illuminate\View\View  $view
   * @return void
   */
  public function compose(View $view)
  {
    $view->with('siteSettings', $this->siteSettings);
  }
}
