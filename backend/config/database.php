<?php

$host = 'localhost';
$db   = 'barberflow';
$user = 'root';
$pass = 'pass'; // ajuste se necessário

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}