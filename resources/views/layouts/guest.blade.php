<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>@yield('title') - {{ config('app.name', 'BarberFlow') }}</title> @vite(['resources/css/app.css', 'resources/js/app.js']) <script> (function() { var theme = localStorage.getItem('barberflow-theme') || 'light'; document.documentElement.setAttribute('data-bs-theme', theme); })(); </script>
</head>
<body> <div class="container"> <main class="py-5"> @yield('content') </main> </div> </body>
</html>

