# API Interna (AJAX)

O BarberFlow utiliza comunicação assíncrona (AJAX/Fetch) para entregar uma experiência moderna sem recarregamento da página.

## Endpoints

### `GET /api/available-times`
Retorna os horários disponíveis cruzando os agendamentos do banco de dados e as horas já passadas no dia de hoje.
- **Parâmetros**: `date` (YYYY-MM-DD), `barber_id` (int).
- **Retorno**: JSON Array com os horários `['09:00', '09:30']`.

*Outros endpoints serão documentados conforme o sistema evoluir para a versão SaaS.*
