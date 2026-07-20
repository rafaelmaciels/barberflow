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
        /* Video Container */
        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            background-color: #000;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        /* Ajuste do scroll oculto na direita */
        .right-column {
            height: 80vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .atendimento-section {
            flex-shrink: 0;
            margin-bottom: 1.5rem;
        }
        .proximos-section {
            flex: 1;
            overflow: hidden;
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
            
            <!-- Coluna Esquerda: Vídeo -->
            <div class="col-md-7 pe-4">
                @if(isset($settings['youtube_queue_video']) && !empty($settings['youtube_queue_video']))
                    <div class="video-container">
                        <iframe src="{{ $settings['youtube_queue_video'] }}" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="video-container d-flex flex-column justify-content-center align-items-center">
                        <i class="fa-solid fa-film fa-5x text-secondary mb-3"></i>
                        <h3 class="text-secondary">Vídeo não configurado</h3>
                    </div>
                @endif
            </div>

            <!-- Coluna Direita: Listas -->
            <div class="col-md-5 border-start border-secondary ps-4 right-column">
                
                <!-- Em Atendimento (Topo) -->
                <div class="atendimento-section">
                    <h3 class="text-success fw-bold mb-3"><i class="fa-solid fa-circle-play blinking me-2"></i> Em Atendimento</h3>
                    <div id="lista-atendimento">
                        <div class="text-center text-muted mt-3">
                            <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                            <p class="mt-2 small">Carregando...</p>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary opacity-25">

                <!-- Próximos da Fila (Abaixo) -->
                <div class="proximos-section">
                    <h4 class="text-warning fw-bold mb-3"><i class="fa-solid fa-list-ol me-2"></i> Próximos</h4>
                    <div id="lista-proximos">
                        <!-- Preenchido via AJAX -->
                    </div>
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
                    html = '<div class="text-muted mt-3 small"><i class="fa-solid fa-mug-hot me-2"></i>A fila está vazia.</div>';
                } else {
                    // Mostrar apenas os 3 próximos na tela devido ao espaço
                    var limite = dados.slice(0, 3);
                    limite.forEach(function(item) {
                        var horaFormatada = item.hora.substring(0, 5);
                        html += `
                        <div class="card-proximo p-2 px-3 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold mb-0">${item.cliente_nome}</h5>
                                    <div class="text-secondary small mb-0"><i class="fa-solid fa-cut me-1"></i> ${item.service.nome}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">Profissional: ${item.barber.nome}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-5 fw-bold text-warning">${horaFormatada}</div>
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
