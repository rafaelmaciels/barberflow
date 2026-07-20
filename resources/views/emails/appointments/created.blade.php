<x-mail::message>
# Confirmação de Agendamento

Olá **{{ $data['cliente_nome'] ?? 'Cliente' }}**,

Seu agendamento foi recebido com sucesso na nossa barbearia!

**Detalhes:**
- **Data:** {{ date('d/m/Y', strtotime($data['data'])) }}
- **Horário:** {{ $data['hora'] }}

Aguardamos você no horário marcado!

Obrigado,<br>
Equipe BarberFlow
</x-mail::message>
