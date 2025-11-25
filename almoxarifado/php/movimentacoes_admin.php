<?php
include_once 'conexao.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "<tr><td colspan='6'>Acesso negado.</td></tr>";
    exit;
}

$termo_busca = null;
if (isset($_GET['busca']) && !empty(trim($_GET['busca']))) {
    $termo_busca = "%" . trim($_GET['busca']) . "%"; 
}

$sql = "SELECT 
            m.data_movimentacao, 
            m.tipo_movimentacao, 
            m.quantidade, 
            m.observacao,
            e.nome AS equipamento_nome,
            f.nome AS funcionario_nome
        FROM movimentacoes m
        LEFT JOIN equipamentos e ON m.id_equipamento = e.id_equipamento
        LEFT JOIN funcionarios f ON m.id_funcionario = f.id_funcionario
";

$parametros = [];
$tipos = "";

if ($termo_busca) {
    $sql .= " WHERE e.nome LIKE ? OR f.nome LIKE ? OR m.observacao LIKE ?";
    $parametros = [$termo_busca, $termo_busca, $termo_busca];
    $tipos = "sss";
}

$sql .= " ORDER BY m.data_movimentacao DESC LIMIT 200"; // Limita a 200 para performance

$declaracao = $conn->prepare($sql);
if ($termo_busca) {
    $declaracao->bind_param($tipos, ...$parametros);
}

$declaracao->execute();
$resultado = $declaracao->get_result();

if ($resultado && $resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        
        $tipo = htmlspecialchars($linha['tipo_movimentacao']);
        $classe_tipo = $tipo == 'saida' ? 'desativar-func' : 'ativar-func'; // Reusa estilos
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars(date('d/m/Y H:i:s', strtotime($linha['data_movimentacao']))) . "</td>";
        echo "<td>" . htmlspecialchars($linha['equipamento_nome'] ?? 'N/D') . "</td>";
        echo "<td>" . htmlspecialchars($linha['funcionario_nome'] ?? 'Sistema') . "</td>";
        echo "<td><span class='acao-btn $classe_tipo' style='pointer-events:none;'>" . ucfirst($tipo) . "</span></td>";
        echo "<td>" . htmlspecialchars($linha['quantidade']) . "</td>";
        echo "<td>" . htmlspecialchars($linha['observacao'] ?? '-') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Nenhuma movimentação encontrada.</td></tr>";
}

$declaracao->close();
$conn->close();
?>