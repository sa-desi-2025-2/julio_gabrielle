<?php
session_start();
include 'conexao.php'; 

if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "<script>alert('Acesso negado.'); window.history.back();</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["numero_prateleira"] ?? '');

    if (empty($nome)) {
        echo "<script>alert('O nome da prateleira é obrigatório.'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO prateleiras (numero_prateleira) VALUES (?)");
    $stmt->bind_param("s", $nome);

    if ($stmt->execute()) {
        echo "
        <script>
          alert('Prateleira adicionada com sucesso!');
          window.location.href = '../php.front/telaeditor.php?view=prateleiras';
        </script>
        ";
    } else {
        echo "<script>alert('Erro ao adicionar: " . $conn->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}
$conn->close();
?>