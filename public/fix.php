<?php
$host = getenv('MYSQLHOST') ?: getenv('DB_HOST');
$port = getenv('MYSQLPORT') ?: getenv('DB_PORT');
$user = getenv('MYSQLUSER') ?: getenv('DB_USERNAME');
$pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD');

echo "<h1>Diagnóstico de Banco de Dados</h1>";

if (!$host || !$user) {
    echo "Erro: Variáveis do Railway não encontradas no ambiente PHP.";
    exit;
}

echo "Tentando conectar ao MySQL em {$host}:{$port}...<br>";

// Tenta executar o fix via comando Node.js
$node_output = shell_exec("node ../fix-db.js 2>&1");
echo "<h2>Resultado do Script Node.js:</h2>";
echo "<pre>$node_output</pre>";

// Tenta conectar nativamente via PDO
echo "<h2>Teste de Conexão PDO:</h2>";
try {
    $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Conexão PDO bem sucedida!";
} catch (PDOException $e) {
    echo "Falha no PDO: " . $e->getMessage();
}
