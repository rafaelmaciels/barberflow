<?php
// Teste básico de funcionamento do PHP
echo json_encode([
    "status" => "PHP funcionando",
    "timestamp" => date('Y-m-d H:i:s'),
    "server" => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    "php_version" => phpversion()
]);
?>