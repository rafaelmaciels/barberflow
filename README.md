# 💈 BarberFlow - Sistema de Agendamento para Barbearia

BarberFlow é um sistema web simples e funcional para gerenciamento de agendamentos em barbearias. O projeto foi desenvolvido com foco em usabilidade, desempenho e aplicação prática, permitindo que clientes agendem horários sem necessidade de cadastro.

## 🌐 Produção

Sistema publicado em:

`https://rafaelmaciel.net/sistemas/barberflow/`

### 🔐 Acesso administrativo padrão

Após a importação padrão do banco:
- Usuário: `admin`
- Senha: `admin123`

Observação:
- Essas são as credenciais padrão definidas no script [barberflow.sql](/home/rafael/barberflow/barberflow.sql). Se o usuário foi alterado diretamente no banco de produção, o acesso pode estar diferente.

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
- Aos domingos o agendamento fica indisponível com a mensagem fixa `FECHADO!`
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
- React.js 18 com Hooks
- Bootstrap 5 para interface responsiva
- Axios para requisições HTTP

### Backend
- PHP 7.3+ com MySQLi
- API REST com CORS
- Sessões para autenticação

### Banco de Dados
- MySQL 8.0
- Estrutura relacional com foreign keys

### Infraestrutura
- Docker & Docker Compose para desenvolvimento
- LiteSpeed para produção

---

## 🚀 Instalação e Desenvolvimento

### Pré-requisitos
- Docker e Docker Compose
- Node.js 16+ (opcional, para desenvolvimento frontend)
- Git

### Desenvolvimento Local

1. **Clone o repositório:**
   ```bash
   git clone <url-do-repositorio>
   cd barberflow
   ```

2. **Configure o ambiente:**
   ```bash
   # Copie o arquivo de exemplo das configurações do banco
   cp backend/config/database.php.example backend/config/database.php
   # Edite com suas credenciais locais
   ```

3. **Inicie os serviços:**
   ```bash
   # Inicia banco MySQL, backend PHP e frontend React
   docker-compose up -d
   ```

4. **Para desenvolvimento frontend (opcional):**
   ```bash
   cd frontend
   npm install
   npm start
   ```

5. **Acesse:**
   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8000

### Produção

1. **Configure o banco MySQL** no servidor
2. **Execute o script SQL:**
   ```sql
   -- Importe barberflow.sql no phpMyAdmin
   ```
3. **Suba os arquivos via FTP:**
   - Use FileZilla para enviar `deploy_pronto/site/` para `public_html/sistemas/barberflow/`
4. **Configure credenciais** no `backend/config/database.php`

---

## 📁 Estrutura do Projeto

```
barberflow/
├── backend/                 # API PHP
│   ├── config/
│   │   ├── database.php     # ⚠️ IGNORADO - configurar manualmente
│   │   └── database.php.example
│   ├── controllers/         # Controladores da API
│   ├── models/             # Modelos (futuro)
│   └── routes/
│       └── api.php         # Definição das rotas
├── frontend/               # Aplicação React
│   ├── public/
│   ├── src/
│   │   ├── components/     # Componentes reutilizáveis
│   │   ├── pages/         # Páginas da aplicação
│   │   ├── services/      # Configuração da API
│   │   └── styles/        # Estilos CSS
│   └── package.json
├── docker/                 # Configurações Docker
├── deploy_pronto/          # 📦 Arquivos prontos para produção
├── docker-compose.yml      # Orquestração local
├── barberflow.sql          # Schema do banco
├── .gitignore             # Arquivos ignorados
└── README.md
```

---

## 🗄️ Modelagem do Banco de Dados

### users
- id (PK)
- username
- password (hashed)

### services
- id (PK)
- name
- price
- duration

### time_slots
- id (PK)
- time

### appointments
- id (PK)
- client_name
- service_id (FK)
- time_slot_id (FK)
- appointment_date
- status (agendado/cancelado/concluido)
- created_at

### settings
- id (PK)
- barbershop_open (boolean)

---

## 🔄 Fluxo da Aplicação

1. Cliente acessa o sistema
2. Informa nome
3. Escolhe serviço
4. Seleciona horário disponível
5. Confirma o agendamento

---

## 🔌 API Endpoints

### Cliente
- `GET /services` - Lista serviços disponíveis
- `GET /time-slots?date=YYYY-MM-DD` - Horários disponíveis para data
- `POST /appointments` - Criar novo agendamento

### Admin
- `POST /login` - Autenticação administrador
- `GET /auth` - Verificar sessão ativa
- `POST /logout` - Encerrar sessão
- `GET /appointments?date=YYYY-MM-DD` - Listar agendamentos
- `PUT /appointments` - Cancelar agendamento
- `GET /settings` - Obter configurações
- `PUT /settings` - Atualizar configurações
- `GET /dashboard?date=YYYY-MM-DD` - Dados do dashboard

---

## ⚙️ Regras de Negócio

- Não permitir dois agendamentos no mesmo horário
- Bloquear automaticamente no `select` os horários já agendados para a data
- Gerar horários dinamicamente em intervalos de 30 minutos nos períodos:
  - 08h00 às 12h00
  - 13h30 às 18h00
- Executar rotina diária às 00h01 para reset de agendamentos antigos e liberação dos intervalos do novo dia
- Bloquear agendamentos com a barbearia fechada
- Bloquear agendamentos aos domingos com a mensagem `FECHADO!`
- Impedir seleção de horários já ocupados
- Validar dados do cliente
- Bloquear horários passados no dia atual

---

## 🔒 Segurança

- ✅ Senhas hasheadas com `password_hash()` (PHP)
- ✅ Sessões seguras para controle de acesso
- ✅ Validação de entrada de dados
- ✅ CORS configurado para domínios específicos
- ✅ Arquivo `database.php` ignorado no Git (.gitignore)
- ✅ Credenciais separadas do código

---

## 📦 Deploy para Produção

### Passos para Deploy

1. **Prepare o banco:**
   - Crie banco MySQL no servidor
   - Importe `barberflow.sql`
   - Atualize senha do admin se necessário

2. **Configure credenciais:**
   ```php
   // backend/config/database.php
   $host = 'localhost'; // ou IP do servidor MySQL
   $db = 'nome_do_banco';
   $user = 'usuario_mysql';
   $pass = 'senha_mysql';
   ```

3. **Suba os arquivos:**
   - Use FileZilla ou similar
   - Envie `deploy_pronto/site/` para `public_html/sistemas/barberflow/`

4. **Teste:**
   - Acesse https://seudominio.com/sistemas/barberflow/
   - Teste login admin e agendamento

### Arquivos Sensíveis
⚠️ **Nunca commite:**
- `backend/config/database.php` (contém senhas)
- Arquivos `.env`
- Credenciais de produção

---

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT.

---

**Desenvolvido com ❤️ para barbearias modernas**

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
- duration

### time_slots
- id
- time

### appointments
- id
- client_name
- service_id
- time_slot_id
- appointment_date
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
- Bloquear agendamentos aos domingos com a mensagem `FECHADO!`
- Impedir seleção de horários já ocupados
- Validar dados do cliente
- Bloquear horários passados no dia atual

---

## 📱 Responsividade

A aplicação será desenvolvida com foco em dispositivos móveis, garantindo uma experiência simples e rápida para o usuário.

---

## 📌 Status do Projeto

✅ Rodando em produção em `https://rafaelmaciel.net/sistemas/barberflow/`

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
