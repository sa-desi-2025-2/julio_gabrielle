<?php
include_once 'conexao.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_admin = (isset($_SESSION['adm']) && $_SESSION['adm'] == 1);
if (!$is_admin) {
    echo "<tr><td colspan='5'>Acesso negado.</td></tr>";
    exit;
}

$id_admin_logado = $_SESSION['id_funcionario'] ?? 0;

$termo_busca = null;
if (isset($_GET['busca']) && !empty(trim($_GET['busca']))) {
    $termo_busca = "%" . trim($_GET['busca']) . "%"; 
}

$sql = "SELECT id_funcionario, nome, cargo, RE, ativo FROM funcionarios";
$params = [];
$types = "";

if ($termo_busca) {
    $sql .= " WHERE nome LIKE ? OR cargo LIKE ? OR RE LIKE ?";
    $params = [$termo_busca, $termo_busca, $termo_busca];
    $types = "sss";
}
$sql .= " ORDER BY nome ASC";

$stmt = $conn->prepare($sql);
if ($termo_busca) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_func = htmlspecialchars($row['id_funcionario']);
        $status = (int)$row['ativo'];
        
        $classe_linha = $status == 0 ? 'inativo' : '';

        echo "<tr class='data-row-func $classe_linha' data-id='" . $id_func . "'>";
        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['cargo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['RE']) . "</td>";
        echo "<td>" . ($status == 1 ? 'Ativo' : 'Desativado') . "</td>";
        
        echo "<td>";
        echo "<button class='acao-btn editar-func' data-id='" . $id_func . "'>Editar</button>";

        if ($id_func != $id_admin_logado) {
            if ($status == 1) {
                echo "<button class='acao-btn desativar-func' data-id='" . $id_func . "'>Desativar</button>";
            } else {
                echo "<button class='acao-btn ativar-func' data-id='" . $id_func . "'>Ativar</button>";
            }
        } else {
            echo "<span class='acao-btn-disabled'> (Admin Atual) </span>";
        }
        
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>Nenhum funcionário encontrado.</td></tr>";
}

$stmt->close();
$conn->close();
?>