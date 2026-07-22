<?php
// Script rápido para exportar o banco de dados do Laravel (estrutura + dados)
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbName = env('DB_DATABASE');
$user = env('DB_USERNAME');
$pass = env('DB_PASSWORD');
$host = env('DB_HOST');
$port = env('DB_PORT');

// Usando o mysqldump do Laragon, se existir
$mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';
if(!file_exists($mysqldumpPath)){
    $dirs = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe');
    if(!empty($dirs)) $mysqldumpPath = $dirs[0];
}

if(file_exists($mysqldumpPath)) {
    $cmd = "\"$mysqldumpPath\" -h $host -P $port -u $user " . ($pass ? "-p$pass " : "") . "$dbName > barberflow_dump.sql";
    exec($cmd);
    echo "Banco exportado com sucesso usando mysqldump! Arquivo: barberflow_dump.sql\n";
} else {
    echo "mysqldump não encontrado no caminho padrão do Laragon.\n";
}
