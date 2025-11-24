<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "Erro: Acesso negado.";
    exit;
}

$id = (int)($_POST['id_prateleira'] ?? 0);

if ($id <= 0) {
    echo "Erro: ID inválido.";
    exit;
}


$stmt = $conn->prepare("DELETE FROM prateleiras WHERE id_prateleira = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Prateleira excluída com sucesso!";
} else {
    echo "Erro ao excluir: " . $conn->error;
}

$stmt->close();
$conn->close();
?>