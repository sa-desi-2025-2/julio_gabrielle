<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "<script>alert('Acesso negado.'); window.history.back();</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id_prateleira"] ?? 0);
    $nome = trim($_POST["numero_prateleira"] ?? '');

    if ($id <= 0 || empty($nome)) {
        echo "<script>alert('Dados inválidos.'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("UPDATE prateleiras SET numero_prateleira = ? WHERE id_prateleira = ?");
    $stmt->bind_param("si", $nome, $id);

    if ($stmt->execute()) {
        echo "
        <script>
          alert('Prateleira atualizada com sucesso!');
          window.location.href = '../php.front/telaeditor.php?view=prateleiras';
        </script>
        ";
    } else {
        echo "<script>alert('Erro ao atualizar: " . $stmt->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}
$conn->close();
?>