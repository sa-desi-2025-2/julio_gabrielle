<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "almoxarifado_utilidades";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
