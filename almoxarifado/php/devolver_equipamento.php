<?php
session_start();
include 'conexao.php'; 

$id_equipamento = (int)($_POST['id_equipamento'] ?? 0);
$qtd_devolvida = (int)($_POST['quantidade'] ?? 0);
$observacao = ($_POST['observacao'] ?? "Devolução sem observação");

$id_funcionario = $_SESSION['id_funcionario'] ?? null;

if ($id_equipamento <= 0 || $qtd_devolvida <= 0 || !$id_funcionario) {
    echo "Dados inválidos ou funcionário não logado.";
    exit;
}

$conn->begin_transaction();

try {
    
  
    $sql_check = "
        SELECT 
            (SUM(CASE WHEN m.tipo_movimentacao = 'saida' THEN m.quantidade ELSE 0 END) - 
             SUM(CASE WHEN m.tipo_movimentacao = 'entrada' THEN m.quantidade ELSE 0 END)) AS qtd_atual
        FROM movimentacoes m
        WHERE m.id_equipamento = ? AND m.id_funcionario = ?
        GROUP BY m.id_equipamento, m.id_funcionario
    ";
    
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $id_equipamento, $id_funcionario);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    $qtd_atual_com_o_usuario = 0;
    if ($result->num_rows > 0) {
        $qtd_atual_com_o_usuario = (int)$result->fetch_assoc()['qtd_atual'];
    }

    if ($qtd_devolvida > $qtd_atual_com_o_usuario) {
        throw new Exception("Erro: Você está tentando devolver $qtd_devolvida, mas você só possui $qtd_atual_com_o_usuario deste item.");
    }
   
    
    $sql_update = "UPDATE equipamentos SET quantidade = quantidade + ? WHERE id_equipamento = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $qtd_devolvida, $id_equipamento);
    $stmt_update->execute();

   
  
    $sql_log = "INSERT INTO movimentacoes (id_equipamento, id_funcionario, tipo_movimentacao, quantidade, observacao) VALUES (?, ?, 'entrada', ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("iiis", $id_equipamento, $id_funcionario, $qtd_devolvida, $observacao);
    $stmt_log->execute();

    
    $conn->commit();
    echo "Equipamento devolvido com sucesso!";

} catch (Exception $e) {
  
    $conn->rollback();
    echo "Erro ao processar a requisição: " . $e->getMessage();
}

$conn->close();
?>