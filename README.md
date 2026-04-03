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

## Deploy em Servidor Compartilhado (FTP + phpMyAdmin)

O projeto foi ajustado para publicar em `https://rafaelmaciel.net/sistemas/barberflow` sem depender de acesso SSH no servidor.

### Estrutura de publicação

Frontend publicado na raiz da pasta do sistema:
`public_html/sistemas/barberflow/`

Backend PHP publicado em:
`public_html/sistemas/barberflow/backend/`

### Passo 1: Gerar o frontend antes do upload

No seu computador local, dentro do projeto:

```bash
cd frontend
npm run build
```

Isso recria a pasta `frontend/build/` com os assets e links apontando para `/sistemas/barberflow/`.

### Passo 2: Criar e importar o banco no phpMyAdmin

1. Acesse o `phpMyAdmin` da hospedagem.
2. Crie um banco MySQL novo.
3. Selecione esse banco no menu lateral.
4. Abra a aba `Importar`.
5. Envie o arquivo [barberflow.sql](/home/rafael/barberflow/barberflow.sql).
6. Clique em `Executar`.

Usuário admin padrão após a importação:
- Usuário: `admin`
- Senha: `admin123`

Observação:
- Alguns provedores usam nomes com prefixo, como `usuario_barberflow`. Use exatamente o nome do banco criado no painel.

### Passo 3: Atualizar credenciais do banco

Edite o arquivo [database.php](/home/rafael/barberflow/backend/config/database.php) antes do upload ou diretamente no servidor e troque:

```php
$host = 'localhost';
$db   = 'SEU_BANCO_MYSQL';
$user = 'SEU_USUARIO_MYSQL';
$pass = 'SUA_SENHA_MYSQL';
$port = 3306;
```

Na maioria das hospedagens compartilhadas:
- `host` = `localhost`
- `db` = nome do banco criado no painel
- `user` = usuário MySQL criado no painel
- `pass` = senha do usuário MySQL

### Passo 4: Fazer deploy via FTP

No FileZilla, conecte ao servidor e envie os arquivos para esta estrutura:

```text
public_html/
└── sistemas/
    └── barberflow/
        ├── index.html
        ├── .htaccess
        ├── asset-manifest.json
        ├── static/
        └── backend/
            ├── index.php
            ├── .htaccess
            ├── test_db.php
            ├── test_php.php
            ├── config/
            ├── controllers/
            ├── models/
            └── routes/
```

Envie exatamente:
- Todo o conteúdo de [frontend/build](/home/rafael/barberflow/frontend/build) para `public_html/sistemas/barberflow/`
- A pasta [backend](/home/rafael/barberflow/backend) inteira para `public_html/sistemas/barberflow/backend/`
- O arquivo [barberflow.sql](/home/rafael/barberflow/barberflow.sql) não precisa ficar publicado no site; ele é só para importação no `phpMyAdmin`

### Passo 5: Testar após o upload

Teste estas URLs:

1. Frontend:
`https://rafaelmaciel.net/sistemas/barberflow/`

2. API de serviços:
`https://rafaelmaciel.net/sistemas/barberflow/backend/index.php/services`

3. Diagnóstico PHP:
`https://rafaelmaciel.net/sistemas/barberflow/backend/test_php.php`

4. Diagnóstico do banco:
`https://rafaelmaciel.net/sistemas/barberflow/backend/test_db.php`

### Observações de compatibilidade

- As rotas internas do frontend e do backend foram ajustadas para a subpasta `/sistemas/barberflow/`
- O backend usa `.htaccess` para rotear requisições em `backend/`
- Não há dependência de Docker, Nginx ou linha de comando no servidor de hospedagem
- Se a hospedagem usar `public_html` ou `private_html`, mantenha a mesma estrutura final da URL pública

### Comandos Git para salvar as alterações

```bash
git add .
git commit -m "Deploy BarberFlow 💈 para servidor compartilhado com suporte a PHP/MySQL"
git push origin main
```

---

## Próximas melhorias

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
