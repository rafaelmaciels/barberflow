# Upload para servidor

Estrutura de destino no servidor:

`private_html/sistemas/barberflow/`

## Pacote minimo desta atualizacao

Se voce quiser subir apenas o necessario desta versao:

- todo o conteudo de `/home/rafael/barberflow/frontend/build/` para `private_html/sistemas/barberflow/`
- `/home/rafael/barberflow/backend/index.php` para `private_html/sistemas/barberflow/backend/index.php`
- `/home/rafael/barberflow/backend/.htaccess` para `private_html/sistemas/barberflow/backend/.htaccess`
- `/home/rafael/barberflow/backend/controllers/AdminController.php` para `private_html/sistemas/barberflow/backend/controllers/AdminController.php`
- `/home/rafael/barberflow/backend/controllers/AppointmentController.php` para `private_html/sistemas/barberflow/backend/controllers/AppointmentController.php`
- `/home/rafael/barberflow/backend/controllers/ScheduleDateController.php` para `private_html/sistemas/barberflow/backend/controllers/ScheduleDateController.php`
- `/home/rafael/barberflow/backend/controllers/ServiceController.php` para `private_html/sistemas/barberflow/backend/controllers/ServiceController.php`
- `/home/rafael/barberflow/backend/controllers/TimeSlotController.php` para `private_html/sistemas/barberflow/backend/controllers/TimeSlotController.php`
- `/home/rafael/barberflow/backend/routes/api.php` para `private_html/sistemas/barberflow/backend/routes/api.php`

## Frontend compilado

Origem local -> destino no servidor

`/home/rafael/barberflow/frontend/build/index.html` -> `private_html/sistemas/barberflow/index.html`
`/home/rafael/barberflow/frontend/build/.htaccess` -> `private_html/sistemas/barberflow/.htaccess`
`/home/rafael/barberflow/frontend/build/asset-manifest.json` -> `private_html/sistemas/barberflow/asset-manifest.json`
`/home/rafael/barberflow/frontend/build/static/css/main.c32417dc.css` -> `private_html/sistemas/barberflow/static/css/main.c32417dc.css`
`/home/rafael/barberflow/frontend/build/static/css/main.c32417dc.css.map` -> `private_html/sistemas/barberflow/static/css/main.c32417dc.css.map`
`/home/rafael/barberflow/frontend/build/static/js/main.c4c7021a.js` -> `private_html/sistemas/barberflow/static/js/main.c4c7021a.js`
`/home/rafael/barberflow/frontend/build/static/js/main.c4c7021a.js.LICENSE.txt` -> `private_html/sistemas/barberflow/static/js/main.c4c7021a.js.LICENSE.txt`
`/home/rafael/barberflow/frontend/build/static/js/main.c4c7021a.js.map` -> `private_html/sistemas/barberflow/static/js/main.c4c7021a.js.map`

## Backend

`/home/rafael/barberflow/backend/index.php` -> `private_html/sistemas/barberflow/backend/index.php`
`/home/rafael/barberflow/backend/.htaccess` -> `private_html/sistemas/barberflow/backend/.htaccess`
`/home/rafael/barberflow/backend/controllers/AdminController.php` -> `private_html/sistemas/barberflow/backend/controllers/AdminController.php`
`/home/rafael/barberflow/backend/controllers/AppointmentController.php` -> `private_html/sistemas/barberflow/backend/controllers/AppointmentController.php`
`/home/rafael/barberflow/backend/controllers/ScheduleDateController.php` -> `private_html/sistemas/barberflow/backend/controllers/ScheduleDateController.php`
`/home/rafael/barberflow/backend/controllers/ServiceController.php` -> `private_html/sistemas/barberflow/backend/controllers/ServiceController.php`
`/home/rafael/barberflow/backend/controllers/TimeSlotController.php` -> `private_html/sistemas/barberflow/backend/controllers/TimeSlotController.php`
`/home/rafael/barberflow/backend/routes/api.php` -> `private_html/sistemas/barberflow/backend/routes/api.php`

## Recomendacao

Mais seguro:

- subir toda a pasta `/home/rafael/barberflow/frontend/build/` para `private_html/sistemas/barberflow/`
- subir toda a pasta `/home/rafael/barberflow/backend/` para `private_html/sistemas/barberflow/backend/`

## Banco de dados

Para uma instalacao que ja existe:

- nao precisa subir o arquivo `barberflow.sql`
- o backend cria automaticamente a coluna `client_phone` em `appointments` se faltar
- o backend cria automaticamente a tabela `blocked_dates` se faltar

Para uma instalacao nova:

- use `/home/rafael/barberflow/barberflow.sql` no phpMyAdmin

## Observacao

O frontend antigo referenciava `main.b3b163f4.js`.
O build atual gerou `main.c4c7021a.js`.
Entao envie o novo `index.html` junto com o novo JS.
