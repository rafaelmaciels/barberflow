# Arquitetura do BarberFlow

O projeto adota os princípios da **Clean Architecture** adaptados para o framework Laravel.

## Estrutura de Diretórios
- `app/Http/Controllers`: Divididos por contexto (Dashboard, Public, Appointments, etc).
- `app/Http/Requests`: Centralizam a validação de formulários (Form Requests).
- `app/Services`: Contêm as regras de negócio. Controllers nunca devem ter lógica de negócio.
- `app/Repositories`: Abstração de acesso ao banco de dados (Eloquent).
- `app/Interfaces`: Contratos que os repositórios implementam.
- `app/Models`: Entidades do Eloquent ORM.

## Fluxo de Dados
1. Rota chama o Controller.
2. Controller injeta o Service.
3. Service executa regras de negócio e chama o Repository via Interface.
4. Repository interage com o Model.
5. Service retorna dados formatados ao Controller.
6. Controller retorna View ou JSON.
