<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $siteSettings->site_name ?? config('app.name', 'ShopBot') }}</title>

        <!-- Favicon with cache busting -->
        <link rel="icon" href="{{ $siteSettings->getFaviconUrl() }}?v={{ time() }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ $siteSettings->getFaviconUrl() }}?v={{ time() }}" type="image/x-icon">

        <!-- Prevent favicon caching -->
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- CSS Variables -->
        <style>
            @include('layouts.css.root')
        </style>

        <!-- Scripts -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
        @vite(['resources/css/app.css'])
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased auth-body">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 auth-container">
            <div class="mb-8">
                <a href="/" class="auth-logo">
                    @if($siteSettings->site_logo)
                        <img src="{{ Storage::url($siteSettings->site_logo) }}" alt="{{ $siteSettings->site_name }}" class="w-24 h-auto">
                    @else
                        <x-application-logo class="w-24 h-24 fill-current auth-logo-color" />
                    @endif
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 auth-card shadow-2xl overflow-hidden sm:rounded-lg">
                <div class="auth-form">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
