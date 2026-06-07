<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Autentikasi' }} - MINI WORKSHOP</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Mini Header inside Page -->
    <header class="bg-white border-b border-gray-100 py-4 px-6 sm:px-12 flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-6 w-auto">
        <span class="font-bold text-lg text-gray-800 tracking-tight">Mini Workshop</span>
    </header>

    <main class="flex flex-col items-center justify-center py-12 px-4">
        <div class="w-full max-w-xl">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
