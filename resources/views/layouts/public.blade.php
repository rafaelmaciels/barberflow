<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Agendamento') - Nome da Barbearia</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>

    <header>
        {{-- Pode ter um menu público simples aqui --}}
    </header>

    <main class="container py-5">
        @yield('content')
    </main>

    <footer class="text-center py-4 mt-5 bg-dark text-white">
        <p class="mb-0">
            BarberFlow &copy; {{ date('Y') }}<br>
            Desenvolvido por Rafael Maciel
        </p>
    </footer>

</body>
</html>
