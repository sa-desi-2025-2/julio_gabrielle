<?php
include 'conexao.php';

$nome = $_POST['nome'] ?? '';
$qtd = (int)($_POST['quantidade'] ?? 0);

if ($nome && $qtd > 0) {
    $sql = "UPDATE equipamentos SET quantidade = GREATEST(quantidade - ?, 0) WHERE nome = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $qtd, $nome);
    if ($stmt->execute()) {
        echo "Equipamento retirado com sucesso!";
    } else {
        echo "Erro ao atualizar equipamento.";
    }
} else {
    echo "Dados inválidos.";
}

$conn->close();
?>
