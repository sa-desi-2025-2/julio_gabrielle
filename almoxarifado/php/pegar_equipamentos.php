<?php
session_start();
include 'conexao.php';

$id_equipamento)( = (int$_POST['id_equipamento'] ?? 0);
$qtd = (int)($_POST['quantidade'] ?? 0);


$id_funcionario = $_SESSION['id_funcionario'] ?? null;

if ($id_equipamento > 0 && $qtd > 0 && $id_funcionario) {
    
    $conn->begin_transaction();

    try {
     
        $sql_update = "UPDATE equipamentos SET quantidade = GREATEST(quantidade - ?, 0) WHERE id_equipamento = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param($nome, $qtd, $id_equipamento);
        $stmt_update->execute();

  
        if ($stmt_update->affected_rows > 0) {
            
           
            $sql_log = "INSERT INTO movimentacoes (id_equipamento, id_funcionario, tipo_movimentacao, quantidade) VALUES (?, ?, 'saida', ?)";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->bind_param($nome, $id_equipamento, $id_funcionario, $qtd);
            $stmt_log->execute();

           
            $conn->commit();
            echo "Equipamento retirado com sucesso!";

        } else {
       
            $conn->rollback();
            echo "Erro: Não foi possível retirar. Verifique o estoque.";
        }
    } catch (Exception $e) {
       
        $conn->rollback();
        echo "Erro ao processar a requisição: " . $e->getMessage();
    }

} else {
    echo "Dados inválidos ou funcionário não logado.";
}

$conn->close();
?>