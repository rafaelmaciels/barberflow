<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Atendimento - {{ $settings['company_name'] ?? config('app.name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #121212; /* Dark Mode para TV */
            color: #ffffff;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            overflow: hidden; /* Evitar rolagem na TV */
        }
        .header-tv {
            background: #1f1f1f;
            border-bottom: 4px solid #0d6efd;
            padding: 1.5rem;
        }
        .card-atendimento {
            background: linear-gradient(145deg, #1e1e1e, #2a2a2a);
            border-left: 5px solid #198754;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            transition: transform 0.3s ease;
        }
        .card-proximo {
            background: #1e1e1e;
            border-left: 5px solid #ffc107;
            border-radius: 10px;
        }
        .clock {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
            text-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
        }
        .blinking {
            animation: blinker 1.5s linear infinite;
        }
        @keyframes blinker {
            50% { opacity: 0; }
        }
    </style>
</head>
<body>

    <div class="header-tv d-flex justify-content-between align-items-center shadow-lg">
        <div class="d-flex align-items-center">
            @if(isset($settings['company_logo']) && $settings['company_logo'])
                <img src="{{ asset($settings['company_logo']) }}" alt="Logo" class="rounded-circle me-3" style="width: 70px; height: 70px; object-fit: cover;">
            @else
                <i class="fa-solid fa-scissors fa-3x me-3 text-primary"></i>
            @endif
            <h1 class="mb-0 fw-bold">{{ $settings['company_name'] ?? 'BarberFlow' }} <span class="fw-light">| Fila de Atendimento</span></h1>
        </div>
        <div class="clock" id="relogio">00:00:00</div>
    </div>

    <div class="container-fluid mt-4 px-5">
        <div class="row">
            
            <!-- Coluna: Em Atendimento (Prioridade) -->
            <div class="col-md-7 pe-5">
                <h2 class="text-success fw-bold mb-4"><i class="fa-solid fa-circle-play blinking me-2"></i> Chamados / Em Atendimento</h2>
                <div id="lista-atendimento">
                    <div class="text-center text-muted mt-5 pt-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Carregando dados ao vivo...</p>
                    </div>
                </div>
            </div>

            <!-- Coluna: Próximos da Fila -->
            <div class="col-md-5 border-start border-secondary ps-5" style="min-height: 80vh;">
                <h3 class="text-warning fw-bold mb-4"><i class="fa-solid fa-list-ol me-2"></i> Próximos da Fila</h3>
                <div id="lista-proximos">
                    <!-- Preenchido via AJAX -->
                </div>
            </div>

        </div>
    </div>

    <!-- Script jQuery e AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            
            // Relógio em tempo real
            setInterval(function() {
                var data = new Date();
                var hora = data.getHours().toString().padStart(2, '0');
                var min = data.getMinutes().toString().padStart(2, '0');
                var seg = data.getSeconds().toString().padStart(2, '0');
                $('#relogio').text(hora + ':' + min + ':' + seg);
            }, 1000);

            // Função para carregar a fila via AJAX
            function carregarFila() {
                $.ajax({
                    url: '{{ route('queue.data') }}',
                    method: 'GET',
                    success: function(response) {
                        renderizarAtendimento(response.em_atendimento);
                        renderizarProximos(response.proximos);
                    },
                    error: function() {
                        console.error('Erro ao buscar dados da fila.');
                    }
                });
            }

            function renderizarAtendimento(dados) {
                var html = '';
                if(dados.length === 0) {
                    html = '<div class="alert alert-dark text-center mt-4">Nenhum cliente em atendimento no momento.</div>';
                } else {
                    dados.forEach(function(item) {
                        html += `
                        <div class="card-atendimento p-4 mb-4">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h1 class="fw-bold text-white mb-1">${item.cliente_nome}</h1>
                                    <h4 class="text-info mb-0"><i class="fa-solid fa-cut me-1"></i> ${item.service.nome}</h4>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="small text-white-50 mb-1">Com o profissional:</div>
                                    <h3 class="text-primary fw-bold mb-0">${item.barber.nome}</h3>
                                </div>
                            </div>
                        </div>`;
                    });
                }
                $('#lista-atendimento').html(html);
            }

            function renderizarProximos(dados) {
                var html = '';
                if(dados.length === 0) {
                    html = '<div class="text-muted mt-4"><i class="fa-solid fa-mug-hot me-2"></i>A fila está vazia.</div>';
                } else {
                    // Mostrar apenas os 5 próximos na tela
                    var limite = dados.slice(0, 5);
                    limite.forEach(function(item) {
                        var horaFormatada = item.hora.substring(0, 5);
                        html += `
                        <div class="card-proximo p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="fw-bold mb-0">${item.cliente_nome}</h4>
                                    <div class="text-secondary mb-1"><i class="fa-solid fa-cut small me-1"></i> ${item.service.nome}</div>
                                    <div class="text-muted small">Profissional: ${item.barber.nome}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-4 fw-bold text-warning">${horaFormatada}</div>
                                </div>
                            </div>
                        </div>`;
                    });
                }
                $('#lista-proximos').html(html);
            }

            // Primeira carga
            carregarFila();

            // Atualização automática a cada 10 segundos
            setInterval(carregarFila, 10000);
        });
    </script>
</body>
</html>
