<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>@yield('title', 'Dashboard') - {{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? config('app.name', 'BarberFlow') }}</title> @vite(['resources/css/app.css', 'resources/js/app.js']) <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <script> (function() { var theme = localStorage.getItem('barberflow-theme') || 'light'; document.documentElement.setAttribute('data-bs-theme', theme); })(); </script>
</head>
<body> <div class="d-flex" id="wrapper"> {{-- Sidebar --}} @include('components.sidebar') {{-- Page Content --}} <div id="page-content-wrapper"> @include('components.navbar') <main class="container-fluid p-4"> @yield('content') </main> @include('components.footer') </div> </div> </body>
</html>

