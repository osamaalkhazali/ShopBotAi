@extends('admin.layouts.admin')

@section('title', 'Site Settings')

@section('breadcrumbs')
    <span class="text-gray-700">Site Settings</span>
@endsection

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Site Settings</h1>
            <p class="text-gray-600">Customize the appearance and branding of your website</p>
        </div>
        <form action="{{ route('admin.settings.reset') }}" method="POST" id="reset-form">
            @csrf
            <button type="button" onclick="confirmReset()" class="py-2 px-4 border border-red-300 bg-red-50 rounded-md shadow-sm text-sm font-medium text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset to Default Values
            </button>
        </form>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form" onsubmit="return confirmSave()">
        @csrf

        <!-- Basic Settings Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Basic Settings
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Site Name -->
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                        <input type="text" name="site_name" id="site_name" value="{{ $settings->site_name }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <p class="mt-1 text-xs text-gray-500">The name of your site that appears in the navbar and browser title</p>
                        @error('site_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Logo -->
                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">Site Logo</label>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($settings->site_logo)
                                    <img src="{{ Storage::url($settings->site_logo) }}" alt="Current Logo" class="h-16 w-auto border rounded p-1">
                                @else
                                    <div class="h-16 w-32 flex items-center justify-center border rounded bg-gray-100 text-gray-400">
                                        <i class="fas fa-image fa-2x"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <input type="file" name="site_logo" id="site_logo"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Recommended size: 200x60px. PNG or SVG format.</p>
                                @error('site_logo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div>
                        <label for="favicon" class="block text-sm font-medium text-gray-700 mb-1">Website Favicon</label>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($settings->favicon)
                                    <img src="{{ Storage::url($settings->favicon) }}" alt="Current Favicon" class="h-16 w-auto border rounded p-1">
                                @else
                                    <div class="h-16 w-16 flex items-center justify-center border rounded bg-gray-100 text-gray-400">
                                        <i class="fas fa-star fa-2x"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <input type="file" name="favicon" id="favicon"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Recommended size: 32x32px or 16x16px. ICO, PNG format.</p>
                                @error('favicon')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Primary Colors Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    Primary Colors
                </h2>
                <p class="mt-1 text-sm text-gray-500 pl-7">Your brand's main colors used for buttons, links, and highlights</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Primary Color -->
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                        <div class="flex items-center">
                            <input type="color" name="primary_color" id="primary_color" value="{{ $settings->primary_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->primary_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="primary_preview" style="background-color: {{ $settings->primary_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(Main brand color for buttons, links, and highlights)</p>
                    </div>

                    <!-- Primary Dark Color -->
                    <div>
                        <label for="primary_dark_color" class="block text-sm font-medium text-gray-700 mb-1">Primary Dark Color</label>
                        <div class="flex items-center">
                            <input type="color" name="primary_dark_color" id="primary_dark_color" value="{{ $settings->primary_dark_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->primary_dark_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="primary_dark_preview" style="background-color: {{ $settings->primary_dark_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(Used for hover states on buttons and links)</p>
                    </div>

                    <!-- Primary Light Color -->
                    <div>
                        <label for="primary_light_color" class="block text-sm font-medium text-gray-700 mb-1">Primary Light Color</label>
                        <div class="flex items-center">
                            <input type="color" name="primary_light_color" id="primary_light_color" value="{{ $settings->primary_light_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->primary_light_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="primary_light_preview" style="background-color: {{ $settings->primary_light_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(For secondary elements, highlights, and accents)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Background Colors Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Background Colors
                </h2>
                <p class="mt-1 text-sm text-gray-500 pl-7">Define the core background colors used throughout your site</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="bg_dark_color" class="block text-sm font-medium text-gray-700 mb-1">Dark Background</label>
                        <div class="flex items-center">
                            <input type="color" name="bg_dark_color" id="bg_dark_color" value="{{ $settings->bg_dark_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->bg_dark_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="bg_dark_preview" style="background-color: {{ $settings->bg_dark_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(Main background for dashboards and app pages)</p>
                    </div>

                    <div>
                        <label for="bg_medium_color" class="block text-sm font-medium text-gray-700 mb-1">Medium Background</label>
                        <div class="flex items-center">
                            <input type="color" name="bg_medium_color" id="bg_medium_color" value="{{ $settings->bg_medium_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->bg_medium_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="bg_medium_preview" style="background-color: {{ $settings->bg_medium_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(For cards, sidebars, and content containers)</p>
                    </div>

                    <div>
                        <label for="bg_light_color" class="block text-sm font-medium text-gray-700 mb-1">Light Background</label>
                        <div class="flex items-center">
                            <input type="color" name="bg_light_color" id="bg_light_color" value="{{ $settings->bg_light_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->bg_light_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="bg_light_preview" style="background-color: {{ $settings->bg_light_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(For inputs, form fields, and lighter elements)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Colors Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Secondary Colors
                </h2>
                <p class="mt-1 text-sm text-gray-500 pl-7">Supporting colors used for secondary elements and contrast</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Secondary Color -->
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                        <div class="flex items-center">
                            <input type="color" name="secondary_color" id="secondary_color" value="{{ $settings->secondary_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->secondary_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="secondary_preview" style="background-color: {{ $settings->secondary_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(For secondary buttons and interface elements)</p>
                    </div>

                    <!-- Secondary Dark Color -->
                    <div>
                        <label for="secondary_dark_color" class="block text-sm font-medium text-gray-700 mb-1">Secondary Dark Color</label>
                        <div class="flex items-center">
                            <input type="color" name="secondary_dark_color" id="secondary_dark_color" value="{{ $settings->secondary_dark_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->secondary_dark_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="secondary_dark_preview" style="background-color: {{ $settings->secondary_dark_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(For hover states on secondary elements)</p>
                    </div>

                    <!-- Secondary Light Color -->
                    <div>
                        <label for="secondary_light_color" class="block text-sm font-medium text-gray-700 mb-1">Secondary Light Color</label>
                        <div class="flex items-center">
                            <input type="color" name="secondary_light_color" id="secondary_light_color" value="{{ $settings->secondary_light_color }}"
                                class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                            <input type="text" value="{{ $settings->secondary_light_color }}"
                                class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                disabled>
                            <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="secondary_light_preview" style="background-color: {{ $settings->secondary_light_color }}"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">(For lighter secondary interface elements)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Colors Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Accent & Status Colors
                </h2>
                <p class="mt-1 text-sm text-gray-500 pl-7">Special colors for attention, system status, and feedback</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Accent Colors -->
                    <div>
                        <h3 class="text-md font-medium text-gray-700 border-b pb-2 mb-4">Accent Color</h3>

                        <div>
                            <label for="accent_color" class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
                            <div class="flex items-center">
                                <input type="color" name="accent_color" id="accent_color" value="{{ $settings->accent_color }}"
                                    class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                                <input type="text" value="{{ $settings->accent_color }}"
                                    class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    disabled>
                                <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="accent_preview" style="background-color: {{ $settings->accent_color }}"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">(For call-to-action buttons and important elements)</p>
                        </div>
                    </div>

                    <!-- Status Colors -->
                    <div>
                        <h3 class="text-md font-medium text-gray-700 border-b pb-2 mb-4">Status Colors</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="success_color" class="block text-sm font-medium text-gray-700 mb-1">Success Color</label>
                                <div class="flex items-center">
                                    <input type="color" name="success_color" id="success_color" value="{{ $settings->success_color }}"
                                        class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                                    <input type="text" value="{{ $settings->success_color }}"
                                        class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        disabled>
                                    <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="success_preview" style="background-color: {{ $settings->success_color }}"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">(For success messages, positive actions)</p>
                            </div>

                            <div>
                                <label for="warning_color" class="block text-sm font-medium text-gray-700 mb-1">Warning Color</label>
                                <div class="flex items-center">
                                    <input type="color" name="warning_color" id="warning_color" value="{{ $settings->warning_color }}"
                                        class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                                    <input type="text" value="{{ $settings->warning_color }}"
                                        class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        disabled>
                                    <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="warning_preview" style="background-color: {{ $settings->warning_color }}"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">(For warnings, notices, alerts)</p>
                            </div>

                            <div>
                                <label for="error_color" class="block text-sm font-medium text-gray-700 mb-1">Error Color</label>
                                <div class="flex items-center">
                                    <input type="color" name="error_color" id="error_color" value="{{ $settings->error_color }}"
                                        class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                                    <input type="text" value="{{ $settings->error_color }}"
                                        class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        disabled>
                                    <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="error_preview" style="background-color: {{ $settings->error_color }}"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">(For error messages, destructive actions)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Text Colors Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    Text Colors
                </h2>
                <p class="mt-1 text-sm text-gray-500 pl-7">Colors used for text content throughout your site</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div>
                            <label for="text_light_color" class="block text-sm font-medium text-gray-700 mb-1">Light Text Color</label>
                            <div class="flex items-center">
                                <input type="color" name="text_light_color" id="text_light_color" value="{{ $settings->text_light_color }}"
                                    class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                                <input type="text" value="{{ $settings->text_light_color }}"
                                    class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    disabled>
                                <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="text_light_preview" style="background-color: {{ $settings->text_light_color }}"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">(For text on dark backgrounds)</p>
                        </div>

                        <div class="mt-4">
                            <label for="text_dark_color" class="block text-sm font-medium text-gray-700 mb-1">Dark Text Color</label>
                            <div class="flex items-center">
                                <input type="color" name="text_dark_color" id="text_dark_color" value="{{ $settings->text_dark_color }}"
                                    class="h-10 w-14 rounded border-gray-300 shadow-sm cursor-pointer">
                                <input type="text" value="{{ $settings->text_dark_color }}"
                                    class="ml-2 w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    disabled>
                                <div class="ml-2 h-10 w-10 rounded-md shadow-sm" id="text_dark_preview" style="background-color: {{ $settings->text_dark_color }}"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">(For text on light backgrounds)</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-3">Preview</p>
                            <div class="flex items-center justify-center gap-4">
                                <div class="h-16 w-16 rounded-md shadow-md" style="background-color: {{ $settings->primary_color }}"></div>
                                <div class="h-16 w-16 rounded-md shadow-md" style="background-color: {{ $settings->secondary_color }}"></div>
                                <div class="h-16 w-16 rounded-md shadow-md" style="background-color: {{ $settings->accent_color }}"></div>
                            </div>
                            <div class="mt-3">
                                <span class="px-3 py-1 text-xs rounded-full shadow-sm" style="background-color: {{ $settings->primary_color }}; color: {{ $settings->text_light_color }}">Primary Button</span>
                                <span class="px-3 py-1 text-xs rounded-full shadow-sm ml-2" style="background-color: {{ $settings->secondary_color }}; color: {{ $settings->text_light_color }}">Secondary Button</span>
                                <span class="px-3 py-1 text-xs rounded-full shadow-sm ml-2" style="background-color: {{ $settings->accent_color }}; color: {{ $settings->text_light_color }}">Accent Button</span>
                            </div>
                            <div class="mt-4 p-3 rounded-md shadow-sm" style="background-color: {{ $settings->bg_medium_color }};">
                                <p class="text-xs" style="color: {{ $settings->text_light_color }}">Text on medium background</p>
                                <div class="mt-2 p-2 rounded-md" style="background-color: {{ $settings->bg_light_color }};">
                                    <p class="text-xs" style="color: {{ $settings->text_light_color }}">Text on light background</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save All Changes
            </button>
        </div>
    </form>

    <!-- Reset Confirmation Modal -->
    <div id="reset-modal" class="fixed inset-0 z-50  items-center justify-center hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" id="reset-backdrop"></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all max-w-lg w-full mx-4 overflow-hidden relative z-10">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reset Settings</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to reset all settings to default values? This cannot be undone.</p>
            </div>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3">
                <button id="reset-cancel" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                    Cancel
                </button>
                <button id="reset-confirm" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none">
                    Reset Settings
                </button>
            </div>
        </div>
    </div>

    <!-- Save Confirmation Modal -->
    <div id="save-modal" class="fixed inset-0 z-50  items-center justify-center hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" id="save-backdrop"></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all max-w-lg w-full mx-4 overflow-hidden relative z-10">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Save Settings</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-700 dark:text-gray-300">Save these settings? This will update colors throughout your site.</p>
            </div>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3">
                <button id="save-cancel" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                    Cancel
                </button>
                <button id="save-confirm" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update preview colors when inputs change
        const colorInputs = document.querySelectorAll('input[type="color"]');
        colorInputs.forEach(input => {
            // Set initial values for text fields
            const colorCode = input.nextElementSibling;
            colorCode.value = input.value;

            // Update preview and text field when color changes
            input.addEventListener('input', function() {
                const id = this.id;
                const previewId = id + '_preview';
                const preview = document.getElementById(previewId);

                if (preview) {
                    preview.style.backgroundColor = this.value;
                }

                // Update the text input next to the color picker
                colorCode.value = this.value;
            });
        });

        // Reset modal functionality
        const resetModal = document.getElementById('reset-modal');
        const resetBackdrop = document.getElementById('reset-backdrop');
        const resetCancelBtn = document.getElementById('reset-cancel');
        const resetConfirmBtn = document.getElementById('reset-confirm');
        const resetForm = document.getElementById('reset-form');

        function openResetModal() {
            resetModal.classList.remove('hidden');
        }

        function closeResetModal() {
            resetModal.classList.add('hidden');
        }

        resetBackdrop.addEventListener('click', closeResetModal);
        resetCancelBtn.addEventListener('click', closeResetModal);
        resetConfirmBtn.addEventListener('click', function() {
            resetForm.submit();
        });

        // Save modal functionality
        const saveModal = document.getElementById('save-modal');
        const saveBackdrop = document.getElementById('save-backdrop');
        const saveCancelBtn = document.getElementById('save-cancel');
        const saveConfirmBtn = document.getElementById('save-confirm');
        const settingsForm = document.getElementById('settings-form');

        function openSaveModal() {
            saveModal.classList.remove('hidden');
        }

        function closeSaveModal() {
            saveModal.classList.add('hidden');
        }

        saveBackdrop.addEventListener('click', closeSaveModal);
        saveCancelBtn.addEventListener('click', closeSaveModal);
        saveConfirmBtn.addEventListener('click', function() {
            settingsForm.removeAttribute('onsubmit');
            settingsForm.submit();
        });
    });

    // Confirmation functions
    function confirmReset() {
        document.getElementById('reset-modal').classList.remove('hidden');
    }

    function confirmSave() {
        document.getElementById('save-modal').classList.remove('hidden');
        return false; // Prevent form from submitting immediately
    }
</script>

<style>
    /* Modal styles */
    #reset-modal, #save-modal {
        z-index: 1050;
    }

    #reset-modal .bg-white, #save-modal .bg-white {
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    }

    #reset-modal:not(.hidden), #save-modal:not(.hidden) {
        display: flex !important;
    }

    #reset-backdrop, #save-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }
</style>
@endsection
