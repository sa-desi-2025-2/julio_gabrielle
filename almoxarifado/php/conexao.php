<?php
$nome = "localhost";
$usuario = "root";
$senha = "";
$banco = "almoxarifado_utilidades";

$conn = new mysqli($nome, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
