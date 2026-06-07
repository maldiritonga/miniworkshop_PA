<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Mini Workshop - {{ $title ?? 'Auth' }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="text-gray-900 antialiased bg-[#F9F9F9]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8">
                <a href="/">
                    <x-application-logo />
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-2xl shadow-gray-100 overflow-hidden sm:rounded-[2.5rem] border border-gray-50">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
