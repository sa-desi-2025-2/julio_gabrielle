<?php
include_once 'conexao.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$eh_admin = (isset($_SESSION['adm']) && $_SESSION['adm'] == 1);
if (!$eh_admin) {
    echo "<tr><td colspan='5'>Acesso negado.</td></tr>";
    exit;
}

$id_admin_logado = $_SESSION['id_funcionario'] ?? 0;

$termo_busca = null;
if (isset($_GET['busca']) && !empty(trim($_GET['busca']))) {
    $termo_busca = "%" . trim($_GET['busca']) . "%"; 
}

$sql = "SELECT id_funcionario, nome, cargo, RE, ativo FROM funcionarios";
$parametros = [];
$tipos = "";

if ($termo_busca) {
    $sql .= " WHERE nome LIKE ? OR cargo LIKE ? OR RE LIKE ?";
    $parametros = [$termo_busca, $termo_busca, $termo_busca];
    $tipos = "sss";
}
$sql .= " ORDER BY nome ASC";

$declaracao = $conn->prepare($sql);
if ($termo_busca) {
    $declaracao->bind_param($tipos, ...$parametros);
}

$declaracao->execute();
$resultado = $declaracao->get_result();

if ($resultado && $resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $id_func = htmlspecialchars($linha['id_funcionario']);
        $status_ativo = (int)$linha['ativo'];
        
        $classe_linha = $status_ativo == 0 ? 'inativo' : '';

        echo "<tr class='data-row-func $classe_linha' data-id='" . $id_func . "'>";
        echo "<td>" . htmlspecialchars($linha['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($linha['cargo']) . "</td>";
        echo "<td>" . htmlspecialchars($linha['RE']) . "</td>";
        echo "<td>" . ($status_ativo == 1 ? 'Ativo' : 'Desativado') . "</td>";
        
        echo "<td>";
        echo "<button class='acao-btn editar-func' data-id='" . $id_func . "'>Editar</button>";

        if ($id_func != $id_admin_logado) {
            if ($status_ativo == 1) {
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

$declaracao->close();
$conn->close();
?>