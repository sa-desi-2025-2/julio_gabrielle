<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "almoxarifado_utilidades";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"];
    $fabricante = $_POST["fabricante"];
    $quantidade = $_POST["quantidade"];
    $descricao = $_POST["descricao"];

   
    $stmt = $conn->prepare("INSERT INTO equipamentos (nome, fabricante, quantidade, descricao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $nome, $fabricante, $quantidade, $descricao);

    if ($stmt->execute()) {
        echo "
        <script>
          alert('Equipamento adicionado com sucesso!');
          window.location.href = '../php/telaeditor.php';
        </script>
        ";
    } else {
        echo "
        <script>
          alert('Erro ao adicionar equipamento: " . $stmt->error . "');
          window.history.back();
        </script>
        ";
    }

    $stmt->close();
}

$conn->close();
?>
