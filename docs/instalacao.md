# Guia de Instalação do BarberFlow

## Requisitos de Servidor
- PHP 8.2 ou 8.3
- MySQL 8+ ou MariaDB
- Composer
- Node.js & NPM

## Passos Iniciais
1. Clone este repositório para a sua máquina ou servidor.
2. Copie o `.env.example` para `.env` e preencha os dados do banco e e-mail.
3. Instale as dependências PHP: `composer install --optimize-autoloader --no-dev`.
4. Instale as dependências Frontend: `npm install` e `npm run build`.
5. Gere a chave da aplicação: `php artisan key:generate`.
6. Rode as migrations e seeders: `php artisan migrate --seed`.

## Servidor Web
Aponte o diretório `DocumentRoot` (Apache) ou `root` (Nginx) para a pasta `public/` do BarberFlow.

## Permissões
O diretório `storage` e `bootstrap/cache` devem possuir permissão de gravação (`775` ou superior).
