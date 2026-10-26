<?php
$servidor = "localhost";
$usuario = "root";
$senha = "usbw";
$banco = "ecommerce_simples";

$conn = new mysqli($servidor, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>