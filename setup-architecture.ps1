$projectRoot = "C:\laragon\www\barberflow"
Set-Location $projectRoot

Write-Host "Iniciando a criação da arquitetura do BarberFlow (FASE 2)..." -ForegroundColor Cyan

# 1. Criação das Pastas
$directories = @(
    "app\Repositories\Interfaces",
    "app\Repositories\Eloquent",
    "app\Services",
    "app\Providers"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Force -Path $dir | Out-Null
        Write-Host "Diretório criado: $dir" -ForegroundColor Green
    }
}

# 2. Criação dos Arquivos - Interfaces
$barberInterface = @"
<?php

namespace App\Repositories\Interfaces;

interface BarberRepositoryInterface
{
    public function all();
    public function find(int `$id);
    public function create(array `$data);
    public function update(int `$id, array `$data);
    public function delete(int `$id);
}
"@
Set-Content -Path "app\Repositories\Interfaces\BarberRepositoryInterface.php" -Value $barberInterface -Encoding UTF8

$serviceInterface = @"
<?php

namespace App\Repositories\Interfaces;

interface ServiceRepositoryInterface
{
    public function all();
    public function find(int `$id);
    public function create(array `$data);
    public function update(int `$id, array `$data);
    public function delete(int `$id);
}
"@
Set-Content -Path "app\Repositories\Interfaces\ServiceRepositoryInterface.php" -Value $serviceInterface -Encoding UTF8

$appointmentInterface = @"
<?php

namespace App\Repositories\Interfaces;

interface AppointmentRepositoryInterface
{
    public function all();
    public function find(int `$id);
    public function create(array `$data);
    public function update(int `$id, array `$data);
    public function delete(int `$id);
}
"@
Set-Content -Path "app\Repositories\Interfaces\AppointmentRepositoryInterface.php" -Value $appointmentInterface -Encoding UTF8

# 3. Criação dos Arquivos - Repositories (Eloquent)
$barberRepo = @"
<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\BarberRepositoryInterface;

class BarberRepository implements BarberRepositoryInterface
{
    public function all()
    {
        // return \App\Models\Barber::all();
    }

    public function find(int `$id)
    {
        // return \App\Models\Barber::find(`$id);
    }

    public function create(array `$data)
    {
        // return \App\Models\Barber::create(`$data);
    }

    public function update(int `$id, array `$data)
    {
        // `$barber = \App\Models\Barber::find(`$id);
        // `$barber->update(`$data);
        // return `$barber;
    }

    public function delete(int `$id)
    {
        // return \App\Models\Barber::destroy(`$id);
    }
}
"@
Set-Content -Path "app\Repositories\Eloquent\BarberRepository.php" -Value $barberRepo -Encoding UTF8

$serviceRepo = @"
<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\ServiceRepositoryInterface;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function all()
    {
        // return \App\Models\Service::all();
    }

    public function find(int `$id)
    {
        // return \App\Models\Service::find(`$id);
    }

    public function create(array `$data)
    {
        // return \App\Models\Service::create(`$data);
    }

    public function update(int `$id, array `$data)
    {
        // `$service = \App\Models\Service::find(`$id);
        // `$service->update(`$data);
        // return `$service;
    }

    public function delete(int `$id)
    {
        // return \App\Models\Service::destroy(`$id);
    }
}
"@
Set-Content -Path "app\Repositories\Eloquent\ServiceRepository.php" -Value $serviceRepo -Encoding UTF8

$appointmentRepo = @"
<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\AppointmentRepositoryInterface;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function all()
    {
        // return \App\Models\Appointment::all();
    }

    public function find(int `$id)
    {
        // return \App\Models\Appointment::find(`$id);
    }

    public function create(array `$data)
    {
        // return \App\Models\Appointment::create(`$data);
    }

    public function update(int `$id, array `$data)
    {
        // `$appointment = \App\Models\Appointment::find(`$id);
        // `$appointment->update(`$data);
        // return `$appointment;
    }

    public function delete(int `$id)
    {
        // return \App\Models\Appointment::destroy(`$id);
    }
}
"@
Set-Content -Path "app\Repositories\Eloquent\AppointmentRepository.php" -Value $appointmentRepo -Encoding UTF8

# 4. Criação dos Arquivos - Services
$barberService = @"
<?php

namespace App\Services;

use App\Repositories\Interfaces\BarberRepositoryInterface;

class BarberService
{
    protected `$barberRepository;

    public function __construct(BarberRepositoryInterface `$barberRepository)
    {
        `$this->barberRepository = `$barberRepository;
    }

    public function getAllBarbers()
    {
        return `$this->barberRepository->all();
    }
}
"@
Set-Content -Path "app\Services\BarberService.php" -Value $barberService -Encoding UTF8

$catalogService = @"
<?php

namespace App\Services;

use App\Repositories\Interfaces\ServiceRepositoryInterface;

class ServiceCatalogService
{
    protected `$serviceRepository;

    public function __construct(ServiceRepositoryInterface `$serviceRepository)
    {
        `$this->serviceRepository = `$serviceRepository;
    }

    public function getAllServices()
    {
        return `$this->serviceRepository->all();
    }
}
"@
Set-Content -Path "app\Services\ServiceCatalogService.php" -Value $catalogService -Encoding UTF8

$appointmentService = @"
<?php

namespace App\Services;

use App\Repositories\Interfaces\AppointmentRepositoryInterface;

class AppointmentService
{
    protected `$appointmentRepository;

    public function __construct(AppointmentRepositoryInterface `$appointmentRepository)
    {
        `$this->appointmentRepository = `$appointmentRepository;
    }

    public function createAppointment(array `$data)
    {
        return `$this->appointmentRepository->create(`$data);
    }
}
"@
Set-Content -Path "app\Services\AppointmentService.php" -Value $appointmentService -Encoding UTF8

# 5. Criação do Provider
$provider = @"
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\BarberRepositoryInterface;
use App\Repositories\Eloquent\BarberRepository;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use App\Repositories\Eloquent\AppointmentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        `$this->app->bind(BarberRepositoryInterface::class, BarberRepository::class);
        `$this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        `$this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
"@
Set-Content -Path "app\Providers\RepositoryServiceProvider.php" -Value $provider -Encoding UTF8
Write-Host "Todos os arquivos de arquitetura criados com sucesso!" -ForegroundColor Green

# 6. Registrar o Provider (Laravel 11 utiliza bootstrap/providers.php)
$providersFilePath = "bootstrap\providers.php"
if (Test-Path $providersFilePath) {
    $providersContent = Get-Content $providersFilePath -Raw
    $providerLine = "    App\Providers\RepositoryServiceProvider::class,"
    
    # Checa se já não foi adicionado
    if ($providersContent -notmatch "RepositoryServiceProvider") {
        # Substitui a última linha para adicionar o novo provider no array
        $providersContent = $providersContent -replace "\];", "`n$providerLine`n];"
        Set-Content -Path $providersFilePath -Value $providersContent -Encoding UTF8
        Write-Host "RepositoryServiceProvider registrado em bootstrap/providers.php." -ForegroundColor Green
    }
}

# 7. Atualizar Autoload
Write-Host "Atualizando autoloader do composer..." -ForegroundColor Cyan
composer dump-autoload

Write-Host "FASE 2 Concluída! Arquitetura criada e configurada com sucesso." -ForegroundColor Yellow
