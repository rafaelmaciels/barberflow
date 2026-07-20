$projectRoot = "C:\laragon\www\barberflow"
Set-Location $projectRoot

Write-Host "Iniciando a FASE 3: Configuração do Banco de Dados..." -ForegroundColor Cyan

# --- 1. POPULATE MODELS ---

$barberModel = @'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'foto',
        'email',
        'telefone',
        'ativo',
        'status',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];
}
'@
Set-Content -Path "app\Models\Barber.php" -Value $barberModel -Encoding UTF8 -NoNewline

$serviceModel = @'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'duracao',
        'valor',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'valor' => 'decimal:2',
    ];
}
'@
Set-Content -Path "app\Models\Service.php" -Value $serviceModel -Encoding UTF8 -NoNewline

$appointmentModel = @'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_nome',
        'cliente_whatsapp',
        'barber_id',
        'service_id',
        'data',
        'hora',
        'status',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
'@
Set-Content -Path "app\Models\Appointment.php" -Value $appointmentModel -Encoding UTF8 -NoNewline

$financialModel = @'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'descricao',
        'valor',
        'data',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data' => 'date',
    ];
}
'@
Set-Content -Path "app\Models\FinancialTransaction.php" -Value $financialModel -Encoding UTF8 -NoNewline


# --- 2. POPULATE MIGRATIONS ---

$barberMigration = @'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('foto')->nullable();
            $table->string('email')->unique();
            $table->string('telefone')->nullable();
            $table->boolean('ativo')->default(true);
            $table->string('status')->default('livre'); // livre, ocupado, ausente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
'@
$bFile = Get-ChildItem -Path "database\migrations" -Filter "*create_barbers_table.php" | Select-Object -First 1
Set-Content -Path $bFile.FullName -Value $barberMigration -Encoding UTF8 -NoNewline

$serviceMigration = @'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->integer('duracao'); // em minutos
            $table->decimal('valor', 10, 2);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
'@
$sFile = Get-ChildItem -Path "database\migrations" -Filter "*create_services_table.php" | Select-Object -First 1
Set-Content -Path $sFile.FullName -Value $serviceMigration -Encoding UTF8 -NoNewline


$appointmentMigration = @'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nome');
            $table->string('cliente_whatsapp');
            $table->foreignId('barber_id')->constrained('barbers')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->date('data');
            $table->time('hora');
            $table->string('status')->default('agendado'); // agendado, concluido, cancelado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
'@
$aFile = Get-ChildItem -Path "database\migrations" -Filter "*create_appointments_table.php" | Select-Object -First 1
Set-Content -Path $aFile.FullName -Value $appointmentMigration -Encoding UTF8 -NoNewline


$financialMigration = @'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // entrada, saida
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
'@
$fFile = Get-ChildItem -Path "database\migrations" -Filter "*create_financial_transactions_table.php" | Select-Object -First 1
Set-Content -Path $fFile.FullName -Value $financialMigration -Encoding UTF8 -NoNewline

Write-Host "Modelos e Migrations atualizados com sucesso!" -ForegroundColor Green

# 3. Remover BOM
Write-Host "Limpando possíveis BOMs..." -ForegroundColor Cyan
Get-ChildItem -Path "app\Models", "database\migrations" -Recurse -Filter "*.php" | ForEach-Object { $content = [System.IO.File]::ReadAllText($_.FullName); [System.IO.File]::WriteAllText($_.FullName, $content) }

Write-Host "Pronto! FASE 3 completada." -ForegroundColor Yellow
