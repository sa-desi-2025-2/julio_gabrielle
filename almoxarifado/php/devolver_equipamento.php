<?php
include 'conexao.php';

$nome = $_POST['nome'] ?? '';
$qtd = (int)($_POST['quantidade'] ?? 0);

if ($nome && $qtd > 0) {
    $sql = "UPDATE equipamentos SET quantidade = quantidade + ? WHERE nome = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $qtd, $nome);
    if ($stmt->execute()) {
        echo "Equipamento devolvido com sucesso!";
    } else {
        echo "Erro ao devolver equipamento.";
    }
} else {
    echo "Dados inválidos.";
}

$conn->close();
?>
