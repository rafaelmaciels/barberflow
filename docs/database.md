# Estrutura do Banco de Dados

O BarberFlow utiliza banco relacional (MySQL/SQLite). 
A estrutura foca no histórico pelo número de WhatsApp, dispensando uma tabela exclusiva de "Clientes".

## Tabelas Principais

### `users`
Usuários administradores e gerentes do painel.

### `settings`
Configurações globais da barbearia (Nome, Cores, Horários).

### `barbers`
Profissionais disponíveis para agendamento.

### `services`
Catálogo de serviços oferecidos com nome, descrição e valor.

### `appointments`
Armazena a reserva de horário. Chaves estrangeiras para `barbers` e `services`.
Guarda o nome e WhatsApp do cliente, data, hora e status (`agendado`, `concluido`, `cancelado`, `nao_compareceu`).

### `financial_transactions`
Registros de entradas e saídas. Criada automaticamente quando um appointment vira `concluido`.

## Futuras Implementações
- `business_hours`: Para regras avançadas de horários abertos/fechados.
- `blocked_times`: Feriados e ausências de barbeiros.
