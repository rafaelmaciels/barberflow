<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Agendar Horário - {{ $settings['company_name'] ?? config('app.name', 'BarberFlow') }}</title> @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- FontAwesome para ícones públicos --> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <style> body { background-color: #f8f9fa; font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; } .hero-section { background: linear-gradient(135deg, #212529 0%, #343a40 100%); color: white; padding: 3rem 0; border-bottom: 5px solid #0d6efd; } .booking-card { margin-top: -40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: none; } .service-option, .barber-option { cursor: pointer; } </style>
</head>
<body> <header class="hero-section text-center"> <div class="container"> @if(isset($settings['company_logo']) && $settings['company_logo']) <img src="{{ asset($settings['company_logo']) }}" alt="Logo" class="img-fluid rounded-circle shadow mb-3" style="width: 100px; height: 100px; object-fit: cover;"> @else <i class="fa-solid fa-scissors fa-4x mb-3 text-primary"></i> @endif <h1 class="fw-bold">{{ $settings['company_name'] ?? 'Barbearia Premium' }}</h1> <p class="lead opacity-75">Agende seu horário de forma rápida e prática</p> </div> </header> <main class="container mb-5"> <div class="row justify-content-center"> <!-- Coluna de Agendamento --> <div class="col-lg-7 mb-4 mb-lg-0"> <div class="card booking-card p-4 p-md-5 h-100"> @if(session('success')) <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 text-center" role="alert"> <i class="fa-solid fa-circle-check fa-3x mb-3"></i> <h4 class="alert-heading fw-bold">Tudo Certo!</h4> <p class="mb-0">{{ session('success') }}</p> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div> @endif @if ($errors->any()) <div class="alert alert-danger rounded-4"> <ul class="mb-0"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul> </div> @endif <form action="{{ route('public.booking.store') }}" method="POST"> @csrf <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user me-2"></i>Seus Dados</h5> <div class="row mb-4"> <div class="col-md-6 mb-3 mb-md-0"> <label class="form-label fw-semibold">Como podemos te chamar? <span class="text-danger">*</span></label> <input type="text" name="cliente_nome" class="form-control form-control-lg" placeholder="Seu Nome Completo" value="{{ old('cliente_nome') }}" required> </div> <div class="col-md-6"> <label class="form-label fw-semibold">Seu melhor WhatsApp <span class="text-danger">*</span></label> <input type="tel" name="cliente_whatsapp" class="form-control form-control-lg" placeholder="(DD) 90000-0000" value="{{ old('cliente_whatsapp') }}" required> </div> </div> <h5 class="fw-bold text-primary mb-3 mt-5"><i class="fa-solid fa-cut me-2"></i>Preferências</h5> <div class="row mb-4"> <div class="col-md-6 mb-3 mb-md-0"> <label class="form-label fw-semibold">Escolha o Serviço <span class="text-danger">*</span></label> <select name="service_id" class="form-select form-select-lg" required> <option value="" disabled selected>Selecione...</option> @foreach($services as $service) <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}> {{ $service->nome }} - R$ {{ number_format($service->valor, 2, ',', '.') }} </option> @endforeach </select> </div> <div class="col-md-6"> <label class="form-label fw-semibold">Profissional <span class="text-danger">*</span></label> <select name="barber_id" id="barber_select" class="form-select form-select-lg" required> <option value="" disabled selected>Quem vai te atender?</option> @foreach($barbers as $barber) <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}> {{ $barber->nome }} </option> @endforeach </select> </div> </div> <h5 class="fw-bold text-primary mb-3 mt-5"><i class="fa-regular fa-clock me-2"></i>Quando?</h5> <div class="row mb-5"> <div class="col-md-6 mb-3 mb-md-0"> <label class="form-label fw-semibold">Data <span class="text-danger">*</span></label> <input type="date" name="data" id="data_input" class="form-control form-control-lg" value="{{ old('data', date('Y-m-d')) }}" required> </div> <div class="col-md-6"> <label class="form-label fw-semibold">Horário <span class="text-danger">*</span></label> <select name="hora" id="hora_select" class="form-select form-select-lg" required> <option value="" disabled selected>Preencha data e profissional...</option> </select> </div> </div> @php $num1 = rand(1, 10); $num2 = rand(1, 10); $sum = $num1 + $num2; @endphp <h5 class="fw-bold text-primary mb-3 mt-5"><i class="fa-solid fa-shield-halved me-2"></i>Verificação Anti-Bot</h5> <div class="row mb-4"> <div class="col-md-12"> <label class="form-label fw-semibold">Quanto é {{ $num1 }} + {{ $num2 }}? <span class="text-danger">*</span></label> <input type="number" id="captcha_input" class="form-control form-control-lg" placeholder="Sua resposta" required> <input type="hidden" id="captcha_answer" value="{{ $sum }}"> </div> </div> <div class="d-grid mt-4"> <button type="submit" id="submit_btn" class="btn btn-primary btn-lg fw-bold rounded-pill p-3 shadow-sm" disabled> <i class="fa-solid fa-calendar-check me-2"></i> Finalizar Agendamento </button> </div> </form> </div> </div> <!-- COLUNA DA DIREITA: FILA AO VIVO --> <div class="col-lg-5 order-1 order-lg-2 mb-4 mb-lg-0 d-none d-md-block"> <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 20px; z-index: 1;"> <div class="card-header bg-dark text-white rounded-top-4 py-3 d-flex justify-content-between align-items-center"> <h5 class="mb-0 fw-bold"><i class="fa-solid fa-users fa-fw me-2 text-warning"></i> Próximos</h5> <span class="badge bg-success rounded-pill px-3">{{ $proximosAtendimentos->count() }} aguardando</span> </div> @if($proximosAtendimentos->isEmpty()) <div class="text-center text-muted py-5"> <i class="fa-solid fa-mug-hot fa-3x mb-3 opacity-25"></i> <h5>Ninguém na fila ainda</h5> <p class="small">Seja o primeiro a agendar para hoje!</p> </div> @else <div class="queue-list" style="max-height: 500px; overflow-y: auto; padding-right: 10px;"> @foreach($proximosAtendimentos as $index => $apt) <div class="card shadow-sm border-0 mb-3" style="background: #f8f9fa;"> <div class="card-body p-3 d-flex align-items-center"> <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; font-weight: bold;"> {{ $index + 1 }} </div> <div class="flex-grow-1"> <h6 class="fw-bold mb-0 text-body">{{ explode(' ', $apt->cliente_nome)[0] }} <span class="fw-normal opacity-50">...</span></h6> <div class="text-muted small"><i class="fa-solid fa-scissors fa-fw me-1"></i>{{ $apt->barber->nome }}</div> </div> <div class="text-end"> <h5 class="fw-bold text-primary mb-0">{{ \Carbon\Carbon::parse($apt->hora)->format('H:i') }}</h5> </div> </div> </div> @endforeach </div> <div class="text-center mt-4"> <p class="text-muted small mb-0"><i class="fa-solid fa-circle-info me-1"></i> Apenas o primeiro nome é exibido por privacidade.</p> </div> @endif </div> </div> </div> </main> <footer class="text-center py-4 text-muted small"> <p class="mb-0"> @if(isset($settings['company_address'])) {{ $settings['company_address'] }} <br> @endif BarberFlow &copy; {{ date('Y') }} - Desenvolvido por Rafael Maciel </p> <div class="mt-2"> <a href="{{ route('login') }}" class="text-decoration-none text-muted">Acesso Restrito</a> </div> </footer> <!-- SweetAlert via CDN para alerts caso o layout não tenha puxado --> <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <script>
document.addEventListener('DOMContentLoaded', function() {
    const captchaInput = document.getElementById('captcha_input');
    const captchaAnswer = document.getElementById('captcha_answer').value;
    const submitBtn = document.getElementById('submit_btn');

    captchaInput.addEventListener('input', function() {
        if (parseInt(this.value) === parseInt(captchaAnswer) && document.getElementById('hora_select').value !=="") {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    });

    const barberSelect = document.getElementById('barber_select');
    const dataInput = document.getElementById('data_input');
    const horaSelect = document.getElementById('hora_select');

    function fetchAvailableTimes() {
        const barberId = barberSelect.value;
        const date = dataInput.value;

        if (!barberId || !date) {
            horaSelect.innerHTML = '<option value="" disabled selected>Preencha data e profissional...</option>';
            return;
        }

        horaSelect.innerHTML = '<option value="" disabled selected>Buscando...</option>';

        fetch(`/api/available-times?barber_id=${barberId}&date=${date}`)
            .then(response => response.json())
            .then(times => {
                horaSelect.innerHTML = '<option value="" disabled selected>Selecione um horário</option>';
                if (times.length === 0) {
                    horaSelect.innerHTML = '<option value="" disabled selected>Nenhum horário disponível</option>';
                } else {
                    times.forEach(time => {
                        const option = document.createElement('option');
                        option.value = time;
                        option.textContent = time;
                        horaSelect.appendChild(option);
                    });
                }
                
                horaSelect.addEventListener('change', function() {
                    if (parseInt(captchaInput.value) === parseInt(captchaAnswer)) {
                        submitBtn.disabled = false;
                    }
                });
            })
            .catch(error => {
                console.error('Erro ao buscar horários:', error);
                horaSelect.innerHTML = '<option value="" disabled selected>Erro ao carregar horários</option>';
            });
    }

    barberSelect.addEventListener('change', fetchAvailableTimes);
    dataInput.addEventListener('change', fetchAvailableTimes);

    if (barberSelect.value && dataInput.value) {
        fetchAvailableTimes();
    }

    function updateLiveQueue() {
        fetch('/api/queue-live')
            .then(response => response.json())
            .then(data => {
                const queueContainer = document.querySelector('.queue-list');
                const badgeCount = document.querySelector('.badge.bg-success');
                if (!queueContainer) return; 

                badgeCount.textContent = data.length + ' aguardando';

                if (data.length === 0) {
                    queueContainer.parentElement.innerHTML = `
                        <div class="card-header bg-dark text-white rounded-top-4 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-users fa-fw me-2 text-warning"></i> Próximos</h5>
                            <span class="badge bg-success rounded-pill px-3">0 aguardando</span>
                        </div>
                        <div class="text-center text-muted py-5">
                            <i class="fa-solid fa-mug-hot fa-3x mb-3 opacity-25"></i>
                            <h5>Ninguém na fila ainda</h5>
                            <p class="small">Seja o primeiro a agendar para hoje!</p>
                        </div>
                    `;
                } else {
                    let html = '';
                    data.forEach((apt, index) => {
                        html += `
                        <div class="card shadow-sm border-0 mb-3" style="background: #f8f9fa;">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    ${index + 1}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0 text-body">${apt.cliente_nome} <span class="fw-normal opacity-50">...</span></h6>
                                    <div class="text-muted small"><i class="fa-solid fa-scissors fa-fw me-1"></i>${apt.barber_nome}</div>
                                </div>
                                <div class="text-end">
                                    <h5 class="fw-bold text-primary mb-0">${apt.hora}</h5>
                                </div>
                            </div>
                        </div>`;
                    });
                    queueContainer.innerHTML = html;
                }
            })
            .catch(error => console.error('Erro ao atualizar fila ao vivo:', error));
    }

    setInterval(updateLiveQueue, 10000);
});
</script>
</body>
</html>

