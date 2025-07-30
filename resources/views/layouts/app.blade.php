<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $siteSettings->site_name ?? config('app.name', 'ShopBot') }}</title>

        <!-- Favicon -->
        @if($siteSettings->favicon)
            <link rel="icon" href="{{ Storage::url($siteSettings->favicon) }}" type="image/x-icon">
            <link rel="shortcut icon" href="{{ Storage::url($siteSettings->favicon) }}" type="image/x-icon">
        @else
            <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
            <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- CSS Variables -->
        <style>
            @include('layouts.css.root')
        </style>

        <!-- Base Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Page Specific Styles -->
        @stack('styles')


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased app-body">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="app-header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="app-main">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
