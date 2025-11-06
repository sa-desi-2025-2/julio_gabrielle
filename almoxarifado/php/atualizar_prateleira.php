<?php
session_start();
include 'conexao.php'; 

if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "Erro: Acesso negado.";
    exit;
}

$id_equipamento = (int)($_POST['id_equipamento'] ?? 0);
$id_prateleira = (int)($_POST['id_prateleira'] ?? 0);

if ($id_equipamento <= 0 || $id_prateleira <= 0) {
    echo "Erro: Dados inválidos (Equipamento ou Prateleira não informados).";
    exit;
}

$check_prat = $conn->prepare("SELECT id_prateleira FROM prateleiras WHERE id_prateleira = ?");
$check_prat->bind_param("i", $id_prateleira);
$check_prat->execute();
$result_prat = $check_prat->get_result();

if ($result_prat->num_rows == 0) {
    echo "Erro: Prateleira de destino não encontrada no banco.";
    exit;
}


$stmt = $conn->prepare("UPDATE equipamentos SET id_prateleira = ? WHERE id_equipamento = ?");
$stmt->bind_param("ii", $id_prateleira, $id_equipamento);

if ($stmt->execute()) {

    $obs = "Localização alterada para prateleira ID: $id_prateleira";
    $id_func = $_SESSION['id_funcionario'];
    $conn->query("INSERT INTO movimentacoes (id_equipamento, id_funcionario, observacao) VALUES ($id_equipamento, $id_func, '$obs')");

    echo "Localização do equipamento atualizada com sucesso!";
} else {
    echo "Erro ao atualizar a localização: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>