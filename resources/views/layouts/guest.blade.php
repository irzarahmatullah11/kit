<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ruang Biaya') }} | Login</title>

        <!-- Menggunakan CSS utama dari aplikasi -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .auth-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 1rem;
                background-color: var(--background-color, #f9fafb);
            }
            .auth-card {
                width: 100%;
                max-width: 420px;
                background: var(--surface-color, #ffffff);
                border: 1px solid var(--border-color, #e5e7eb);
                border-radius: 12px;
                padding: 2rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            .auth-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                text-decoration: none;
                margin-bottom: 2rem;
            }
        </style>
    </head>
    <body>
        <div class="app-shell auth-wrapper">
            <div class="auth-card">


                {{ $slot }}
            </div>
        </div>
    </body>
</html>