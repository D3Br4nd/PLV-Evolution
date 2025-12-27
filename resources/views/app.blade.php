<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Pro Loco Venticanese - L'evoluzione della tradizione.">
        
        <title inertia>{{ config('app.name', 'Pro Loco Venticanese') }}</title>

        <!-- Fonts (Example Inter, can be replaced) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
        
        <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script> <!-- Optional -->

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased h-full">
        @inertia
    </body>
</html>
