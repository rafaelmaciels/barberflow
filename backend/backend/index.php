<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inicia sessão (OBRIGATÓRIO para login)
session_start();

// Exibir erros (debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers padrão
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Importa rotas
require_once "routes/api.php";