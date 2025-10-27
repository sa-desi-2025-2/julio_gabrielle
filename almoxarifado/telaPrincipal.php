<?php
$host = 'localhost';
$db   = 'almoxarifado_utilidades';
$usuario = 'root';
$senha = '';

$conn = new mysqli($host, $usuario, $senha, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


$sql = "
SELECT e.nome AS equipamento,
       e.localizacao,
       e.quantidade,
       e.marca,
       f.nome AS responsavel
FROM equipamentos e
LEFT JOIN funcionarios f ON e.responsavel_id = f.id
";

$result = $conn->query($sql);
?>