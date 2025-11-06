<?php
session_start();
include 'conexao.php';


if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "Erro: Acesso negado.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $id_funcionario = (int)($_POST["id_funcionario"] ?? 0);
    
    $novo_status = (int)($_POST["status"] ?? -1); 
    $id_admin_logado = $_SESSION['id_funcionario'] ?? 0;

    if ($id_funcionario <= 0 || !in_array($novo_status, [0, 1])) {
        echo "Erro: Dados inválidos.";
        exit;
    }

    if ($id_funcionario == $id_admin_logado) {
        echo "Erro: Você não pode desativar a si mesmo.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE funcionarios SET ativo = ? WHERE id_funcionario = ?");
    $stmt->bind_param("ii", $novo_status, $id_funcionario);

    if ($stmt->execute()) {
        echo $novo_status == 0 ? "Funcionário desativado." : "Funcionário ativado.";
    } else {
        echo "Erro ao atualizar status: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>