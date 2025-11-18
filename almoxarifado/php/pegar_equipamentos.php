<?php
session_start();
include 'conexao.php'; 

$id_equipamento = (int)($_POST['id_equipamento'] ?? 0);
$qtd_solicitada = (int)($_POST['quantidade'] ?? 0);
$observacao = ($_POST['observacao'] ?? "Retirada sem observação");

$id_funcionario = $_SESSION['id_funcionario'] ?? null;


if (!$id_funcionario) {
    echo "Erro: Usuário não está logado.";
    exit;
}
if ($id_equipamento <= 0 || $qtd_solicitada <= 0) {
    echo "Dados inválidos. Verifique o equipamento e a quantidade.";
    exit;
}

$conn->begin_transaction();

try {
    
    $sql_check = "SELECT quantidade FROM equipamentos WHERE id_equipamento = ? FOR UPDATE";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_equipamento);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Equipamento não encontrado.");
    }
    
    $row = $result->fetch_assoc();
    $qtd_estoque = (int)$row['quantidade'];

    if ($qtd_solicitada > $qtd_estoque) {
        throw new Exception("Quantidade indisponível. Estoque atual: " . $qtd_estoque);
    }
    
    $sql_update = "UPDATE equipamentos SET quantidade = quantidade - ? WHERE id_equipamento = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $qtd_solicitada, $id_equipamento);
    $stmt_update->execute();

    $sql_log = "INSERT INTO movimentacoes (id_equipamento, id_funcionario, tipo_movimentacao, quantidade, observacao) VALUES (?, ?, 'saida', ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("iiis", $id_equipamento, $id_funcionario, $qtd_solicitada, $observacao);
    $stmt_log->execute();

    $conn->commit();
    echo "Equipamento retirado com sucesso!";

} catch (Exception $e) {

    $conn->rollback();
    echo "Erro ao processar a requisição: " . $e->getMessage();
}

$conn->close();
?>