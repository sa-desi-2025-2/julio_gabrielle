<?php

$host = 'localhost'; 
$db   = 'almoxarifado_utilidades';
$usuario = 'root';
$senha = '';

$conn = new mysqli($host, $usuario, $senha, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$nome = $_POST['nome'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$re = $_POST['re'] ?? '';

$ehAdmin = 0;
if (isset($_POST['administrador']) && $_POST['administrador'] == 'on') {
    $ehAdmin = 1;
}


$stmt = $conn->prepare("INSERT INTO funcionarios (nome, cargo, re, adm) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $nome, $cargo, $re, $ehAdmin);

if ($stmt->execute()) {
  
    echo "
    <script>
      alert('Funcionário registrado com sucesso!');
      window.location.href = '../php.front/telaeditor.php';
    </script>
    ";
} else {
 
     echo "
    <script>
      alert('Erro ao registrar funcionário: " . $conn->error . "');
      window.history.back();
    </script>
    ";
}

$stmt->close();
$conn->close();
?>