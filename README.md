# 💈 BarberFlow - Sistema de Agendamento para Barbearia

BarberFlow é um sistema web simples e funcional para gerenciamento de agendamentos em barbearias. O projeto foi desenvolvido com foco em usabilidade, desempenho e aplicação prática, permitindo que clientes agendem horários sem necessidade de cadastro.

## 🚀 Objetivo

Criar uma aplicação realista para portfólio que resolva um problema comum: organização de atendimentos em barbearias, substituindo agendas manuais ou atendimentos via WhatsApp.

---

## 🧠 Funcionalidades

### 👤 Cliente
- Agendamento sem necessidade de login
- Seleção de serviço:
  - Corte de cabelo
  - Barba
  - Combo (cabelo + barba)
- Navbar com identidade visual `💈 BarberFlow`
- Favicon com emoji `💈`
- Escolha de horários disponíveis em intervalos de 30 minutos
- Horários de atendimento: 08h00 às 12h00 e 13h30 às 18h00
- Título dinâmico de horários no formato:
  - `Horário - Quarta-feira 01/04/2026`
- Confirmação de agendamento

### 🔐 Administrador
- Cadastro e edição de serviços e preços
- Definição de horários disponíveis
- Visualização de agendamentos
- Controle de funcionamento da barbearia (abrir/fechar)
- Bloqueio de horários específicos

---

## 🏗️ Tecnologias utilizadas

### Frontend
- React.js

### Backend
- PHP

### Banco de Dados
- MySQL

---

## 📁 Estrutura do Projeto

### Frontend
/src
/components
/pages
/services
/styles


### Backend
/api
/config
/controllers
/models
/routes


---

## 🗄️ Modelagem do Banco de Dados

### services
- id
- name
- price
- active

### time_slots
- id
- time
- available

### appointments
- id
- client_name
- service_id
- time_slot_id
- date
- status
- created_at

### settings
- id
- barbershop_open

---

## 🔄 Fluxo da Aplicação

1. Cliente acessa o sistema
2. Informa nome
3. Escolhe serviço
4. Seleciona horário disponível
5. Confirma o agendamento

---

## 🔌 API (Endpoints)

### Cliente
- `GET /services`
- `GET /time-slots`
- `POST /appointments`

### Admin
- `GET /appointments`
- `PUT /services`
- `PUT /time-slots`
- `PUT /settings`

---

## ⚙️ Regras de Negócio

- Não permitir dois agendamentos no mesmo horário
- Bloquear automaticamente no `select` os horários já agendados para a data
- Gerar horários dinamicamente em intervalos de 30 minutos nos períodos:
  - 08h00 às 12h00
  - 13h30 às 18h00
- Executar rotina diária às 00h01 para reset de agendamentos antigos e liberação dos intervalos do novo dia
- Bloquear agendamentos com a barbearia fechada
- Impedir seleção de horários já ocupados
- Validar dados do cliente
- Bloquear horários passados no dia atual

---

## 📱 Responsividade

A aplicação será desenvolvida com foco em dispositivos móveis, garantindo uma experiência simples e rápida para o usuário.

---

## 📌 Status do Projeto

🚧 Em desenvolvimento

---

## 💡 Próximas melhorias

- Dashboard administrativo completo
- Filtro de agendamentos por data
- Notificações (ex: WhatsApp)
- Cancelamento de agendamento
- Geração de código de confirmação

---

## 👨‍💻 Autor

Rafael Maciel

- Experiência em suporte técnico, desenvolvimento web e atendimento ao cliente
- Foco em soluções práticas e funcionais

---

## 📄 Licença

Este projeto está sob a licença MIT.
