<x-mail::message>
# Confirmação de Agendamento Olá **{{ $data['cliente_nome'] ?? 'Cliente' }}**, agendou atendimento com sucesso na nossa barbearia! **Detalhes:**
- **Data:** {{ date('d/m/Y', strtotime($data['data'])) }}
- **Horário:** {{ $data['hora'] }} 
</x-mail::message>

