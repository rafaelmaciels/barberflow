<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'BarberFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>

    <div class="d-flex" id="wrapper">

        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Page Content --}}
        <div id="page-content-wrapper">

            @include('components.navbar')

            <main class="container-fluid p-4">
                @yield('content')
            </main>

            @include('components.footer')
        </div>
    </div>

</body>
</html>
