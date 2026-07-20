# ==================================================
# BARBERFLOW
# Estrutura inicial do projeto
# Laravel 13 + Bootstrap 5
# ==================================================

Write-Host ""
Write-Host "======================================="
Write-Host "      BARBERFLOW STRUCTURE SETUP"
Write-Host "======================================="
Write-Host ""


# -------------------------------
# PASTAS APP
# -------------------------------

$folders = @(

"app/Enums",
"app/Helpers",
"app/Services",
"app/Repositories",
"app/Repositories/Interfaces",
"app/Repositories/Eloquent",
"app/Traits",
"app/Observers",
"app/Policies",
"app/Jobs",
"app/Notifications",
"app/Mail",


# Controllers

"app/Http/Controllers/Auth",
"app/Http/Controllers/Dashboard",
"app/Http/Controllers/Barbers",
"app/Http/Controllers/Services",
"app/Http/Controllers/Appointments",
"app/Http/Controllers/Finance",
"app/Http/Controllers/Reports",
"app/Http/Controllers/Settings",
"app/Http/Controllers/Installation",
"app/Http/Controllers/Public",


# Requests

"app/Http/Requests/Auth",
"app/Http/Requests/Barbers",
"app/Http/Requests/Services",
"app/Http/Requests/Appointments",
"app/Http/Requests/Finance",
"app/Http/Requests/Settings",



# RESOURCES

"resources/views/layouts",
"resources/views/components",

"resources/views/components/forms",
"resources/views/components/tables",
"resources/views/components/cards",


"resources/views/auth",

"resources/views/dashboard",

"resources/views/barbers",

"resources/views/services",

"resources/views/appointments",

"resources/views/finance",

"resources/views/reports",

"resources/views/settings",

"resources/views/installation",

"resources/views/public",



# JS

"resources/js/modules",
"resources/js/pages",



# CSS

"resources/css/components",



# PUBLIC

"public/assets/css",
"public/assets/js",
"public/assets/images",

"public/uploads",
"public/uploads/logo",
"public/uploads/barbers",
"public/uploads/services",
"public/uploads/temp",



# STORAGE

"storage/backups",
"storage/exports",
"storage/imports",



# DATABASE

"database/backup"

)



foreach($folder in $folders)
{

    if(!(Test-Path $folder))
    {

        New-Item -ItemType Directory -Path $folder | Out-Null

        Write-Host "Criado: $folder"

    }

}



# ==================================================
# CRIAR ARQUIVOS BASE
# ==================================================


$files = @(


"app/Services/AppointmentService.php",
"app/Services/BarberService.php",
"app/Services/FinancialService.php",
"app/Services/InstallationService.php",
"app/Services/EmailService.php",


"app/Repositories/Interfaces/BarberRepositoryInterface.php",
"app/Repositories/Interfaces/ServiceRepositoryInterface.php",
"app/Repositories/Interfaces/AppointmentRepositoryInterface.php",


"app/Repositories/Eloquent/BarberRepository.php",
"app/Repositories/Eloquent/ServiceRepository.php",
"app/Repositories/Eloquent/AppointmentRepository.php",



"resources/views/layouts/app.blade.php",
"resources/views/layouts/guest.blade.php",


"resources/views/components/navbar.blade.php",
"resources/views/components/sidebar.blade.php",
"resources/views/components/footer.blade.php",

"resources/views/components/cards/stat.blade.php",

"resources/views/components/forms/input.blade.php",

"resources/views/components/tables/table.blade.php",



"resources/views/dashboard/index.blade.php",

"resources/views/barbers/index.blade.php",

"resources/views/services/index.blade.php",

"resources/views/appointments/index.blade.php",

"resources/views/finance/index.blade.php",

"resources/views/reports/index.blade.php",

"resources/views/settings/index.blade.php",



"resources/js/modules/dashboard.js",
"resources/js/modules/calendar.js",
"resources/js/modules/datatables.js",


"resources/css/components/sidebar.css",
"resources/css/components/cards.css"


)



foreach($file in $files)
{

    if(!(Test-Path $file))
    {

        New-Item -ItemType File -Path $file | Out-Null

        Write-Host "Arquivo criado: $file"

    }

}



# ==================================================
# CRIAR ARQUIVO DE VERSAO
# ==================================================

@"

BARBERFLOW

Versão inicial da estrutura.

Laravel 13
Bootstrap 5
MySQL

Desenvolvido por Rafael Maciel.

"@ | Out-File "BARBERFLOW.md" -Encoding UTF8



Write-Host ""

Write-Host "======================================="
Write-Host " Estrutura BarberFlow criada com sucesso"
Write-Host "======================================="

Write-Host ""
