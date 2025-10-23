<?php

$host = 'localhost'; 
$db   = 'almoxarifado_utilidades';
$usuario = 'root';
$senha = '';

$conn = new mysqli($host, $usuario, $senha, $db);


if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


$nome = $conn->real_escape_string($_POST['nome']);
$cargo = $conn->real_escape_string($_POST['cargo']);
$re = $conn->real_escape_string($_POST['re']);


$sql = "INSERT INTO funcionarios (nome, cargo ,re) VALUES ('$nome', '$cargo', '$re')"; 

if ($conn->query($sql) === TRUE) {
    echo "Funcionário registrado com sucesso!";
} else {
    echo "Erro: " . $conn->error;
}


$conn->close();
?>