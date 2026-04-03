# BarberFlow - Deploy via FTP

URL final do sistema:
`https://rafaelmaciel.net/sistemas/barberflow/`

## 1. Preparar no computador local

Gerar o frontend compilado:

```bash
cd frontend
npm run build
```

O build será criado em `frontend/build/` já apontando para `/sistemas/barberflow/`.

## 2. Configurar o banco no phpMyAdmin

1. Crie um banco MySQL no painel da hospedagem.
2. Abra o `phpMyAdmin`.
3. Selecione o banco criado.
4. Use a aba `Importar`.
5. Importe o arquivo `barberflow.sql`.

Usuário admin padrão após a importação:
- Usuário: `admin`
- Senha: `admin123`

## 3. Atualizar credenciais do MySQL

Edite `backend/config/database.php`:

```php
$host = 'localhost';
$db   = 'SEU_BANCO_MYSQL';
$user = 'SEU_USUARIO_MYSQL';
$pass = 'SUA_SENHA_MYSQL';
$port = 3306;
```

## 4. Enviar por FTP

Destino:
`public_html/sistemas/barberflow/`

Estrutura final:

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

Envie:
- Conteúdo de `frontend/build/` para `public_html/sistemas/barberflow/`
- Pasta `backend/` para `public_html/sistemas/barberflow/backend/`

## 5. Testar

- Frontend:
  `https://rafaelmaciel.net/sistemas/barberflow/`
- API:
  `https://rafaelmaciel.net/sistemas/barberflow/backend/index.php/services`
- PHP:
  `https://rafaelmaciel.net/sistemas/barberflow/backend/test_php.php`
- Banco:
  `https://rafaelmaciel.net/sistemas/barberflow/backend/test_db.php`
