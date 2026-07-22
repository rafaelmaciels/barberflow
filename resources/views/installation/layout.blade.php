<!DOCTYPE html>
<html lang="pt-BR">
<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Instalação do BarberFlow</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <style> body { background-color: #f8f9fa; } .install-card { max-width: 600px; margin: 50px auto; border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); } .install-header { background: #212529; color: white; border-radius: 12px 12px 0 0 !important; text-align: center; padding: 20px; } </style>
</head>
<body> <div class="container"> <div class="card install-card"> <div class="card-header install-header"> <h3 class="mb-0">BarberFlow Setup</h3> <small>@yield('step_title', 'Assistente de Instalação')</small> </div> <div class="card-body p-4"> @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif @if ($errors->any()) <div class="alert alert-danger"> <ul class="mb-0"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul> </div> @endif @yield('content') </div> </div> </div>
</body>
</html>

