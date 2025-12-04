<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            input:-webkit-autofill,
            input:-webkit-autofill:hover,
            input:-webkit-autofill:focus {
                -webkit-box-shadow: 0 0 0px 1000px #f9fafb inset;
                -webkit-text-fill-color: #111827;
                transition: background-color 5000s ease-in-out 0s;
            }

            .dark input:-webkit-autofill,
            .dark input:-webkit-autofill:hover,
            .dark input:-webkit-autofill:focus {
                -webkit-box-shadow: 0 0 0px 1000px #374151 inset;
                -webkit-text-fill-color: white;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg " style="border: 1px solid #16a34a">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
