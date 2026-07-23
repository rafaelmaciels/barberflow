<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Painel de Atendimento - {{ $settings['company_name'] ?? config('app.name') }}</title> @vite(['resources/css/app.css', 'resources/js/app.js']) <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <style>
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
    
    /* Layout Unificado da Lista */
    .queue-list-container {
        height: 80vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* Custom Scrollbar for the list */
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Card Destaque: Em Atendimento */
    .card-atendimento {
        background: linear-gradient(145deg, #1e1e1e, #2a2a2a);
        border-left: 6px solid #198754;
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        animation: pulse-border 2s infinite;
    }
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }

    /* Card Padrão: Próximos */
    .card-proximo {
        background: #1e1e1e;
        border-left: 4px solid #6c757d;
        border-radius: 8px;
    }
    
    .card-proximo:nth-child(1) {
        border-left-color: #ffc107; /* O primeirão da fila ganha um amarelo */
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

        <!-- Coluna Direita: Lista Unificada -->
        <div class="col-md-5 border-start border-secondary ps-4">
            <h3 class="text-white fw-bold mb-4"><i class="fa-solid fa-list-ul me-2 text-primary"></i> Chamadas</h3>
            <div id="queue-list-container" class="queue-list-container">
                <div class="text-center text-muted mt-5">
                    <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                    <p class="mt-2">Sincronizando fila...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    setInterval(function() {
        var data = new Date();
        var hora = data.getHours().toString().padStart(2, '0');
        var min = data.getMinutes().toString().padStart(2, '0');
        var seg = data.getSeconds().toString().padStart(2, '0');
        $('#relogio').text(hora + ':' + min + ':' + seg);
    }, 1000);

    function carregarFila() {
        $.ajax({
            url: '{{ route('queue.data') }}',
            method: 'GET',
            success: function(response) {
                renderizarFila(response.em_atendimento, response.proximos);
            },
            error: function() {
                console.error('Erro ao buscar dados da fila.');
            }
        });
    }

    function renderizarFila(emAtendimento, proximos) {
        var html = '';
        
        if (emAtendimento.length === 0 && proximos.length === 0) {
            html = '<div class="alert alert-dark text-center mt-4 border-0 opacity-75"><i class="fa-solid fa-mug-hot fa-2x mb-2"></i><br>A fila está vazia no momento.</div>';
            $('#queue-list-container').html(html);
            return;
        }

        html += '<div style="flex-shrink: 0;">';
        
        // Renderiza Em Atendimento
        emAtendimento.forEach(function(item) {
            html += `
            <div class="card-atendimento p-3 mb-3">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success bg-opacity-25 text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-circle-play blinking me-1"></i> Em Atendimento
                    </span>
                </div>
                <div class="mb-2">
                    <h1 class="fw-bold text-white mb-1" style="font-size: 2.2rem; line-height: 1.1; word-wrap: break-word;">${item.cliente_nome}</h1>
                    <div class="text-info mb-0 fw-light" style="font-size: 1rem;"><i class="fa-solid fa-cut me-2"></i>${item.service.nome}</div>
                </div>
                <div class="d-flex align-items-center border-top border-secondary border-opacity-50 pt-2 mt-1">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                        <i class="fa-solid fa-user-tie fs-6"></i>
                    </div>
                    <div>
                        <div class="text-white-50 text-uppercase tracking-wider" style="font-size: 0.65rem; letter-spacing: 1px;">Profissional</div>
                        <div class="text-white fw-bold mb-0" style="font-size: 0.9rem;">${item.barber.nome}</div>
                    </div>
                </div>
            </div>`;
        });
        
        html += '</div>'; // End flex-shrink: 0 container
        
        // Inicia container rolável para Próximos
        html += '<div style="flex: 1; overflow-y: auto; overflow-x: hidden; padding-right: 5px;" class="custom-scroll d-flex flex-column gap-2">';

        // Renderiza os Próximos
        var limite = proximos.slice(0, 10); // Aumentei o limite para 10, já que agora é rolável
        limite.forEach(function(item, index) {
            var horaFormatada = item.hora.substring(0, 5);
            var isNext = (index === 0 && emAtendimento.length === 0) ? 'text-warning' : 'text-white-50';
            var iconNext = (index === 0 && emAtendimento.length === 0) ? '<i class="fa-solid fa-angles-right blinking me-1"></i> ' : '';

            html += `
            <div class="card-proximo p-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pe-2" style="min-width: 0; flex: 1;">
                        <h5 class="fw-bold text-white mb-1 text-truncate">${iconNext}${item.cliente_nome}</h5>
                        <div class="text-secondary small mb-1 text-truncate" style="font-size: 0.8rem;"><i class="fa-solid fa-cut me-1"></i> ${item.service.nome}</div>
                        <div class="text-white-50 small mb-0 text-truncate" style="font-size: 0.75rem;"><i class="fa-solid fa-user-tie me-1"></i> ${item.barber.nome}</div>
                    </div>
                    <div class="text-end ps-2 border-start border-secondary border-opacity-25" style="min-width: 80px;">
                        <div class="text-uppercase text-white-50" style="font-size: 0.6rem; letter-spacing: 1px;">Horário</div>
                        <div class="fs-4 fw-bold ${isNext}">${horaFormatada}</div>
                    </div>
                </div>
            </div>`;
        });

        html += '</div>'; // End scroll container

        $('#queue-list-container').html(html);
    }

    carregarFila();
    setInterval(carregarFila, 10000);
});
</script>
</body>
</html>
