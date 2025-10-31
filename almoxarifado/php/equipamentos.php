<?php
include 'conexao.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_admin = (isset($_SESSION['adm']) && $_SESSION['adm'] == 1);

$view = $_GET['view'] ?? 'todos';

$sql = "";

$sql_livres = "
    SELECT 
        e.id_equipamento,
        e.nome,
        e.fabricante,
        e.quantidade,
        p.numero_prateleira AS localizacao,
        '-' AS responsavel,
        'livre' AS tipo_item
    FROM equipamentos e
    LEFT JOIN prateleiras p ON e.id_prateleira = p.id_prateleira
    WHERE e.quantidade > 0
";

$sql_ocupados = "
    SELECT 
        e.id_equipamento,
        e.nome,
        e.fabricante,
        (SUM(CASE WHEN m.tipo_movimentacao = 'saida' THEN m.quantidade ELSE 0 END) - 
         SUM(CASE WHEN m.tipo_movimentacao = 'entrada' THEN m.quantidade ELSE 0 END)) AS quantidade,
        p.numero_prateleira AS localizacao,
        f.nome AS responsavel,
        'ocupado' AS tipo_item
    FROM movimentacoes m
    JOIN equipamentos e ON m.id_equipamento = e.id_equipamento
    JOIN funcionarios f ON m.id_funcionario = f.id_funcionario
    LEFT JOIN prateleiras p ON e.id_prateleira = p.id_prateleira
    GROUP BY m.id_equipamento, m.id_funcionario, f.nome, e.nome, e.fabricante, p.numero_prateleira
    HAVING quantidade > 0
";


if ($view == 'livres') {
    $sql = $sql_livres . " ORDER BY e.nome ASC";
} else if ($view == 'ocupados') {
    $sql = $sql_ocupados . " ORDER BY responsavel ASC, nome ASC";
} else { 
    $sql = $sql_livres . " UNION ALL " . $sql_ocupados . " ORDER BY nome ASC, responsavel ASC";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tipo = $row['tipo_item'];
        $id_equip = htmlspecialchars($row['id_equipamento']);
       
        echo "<tr class='data-row' data-id='" . $id_equip . "' tabindex='0' aria-expanded='false'>";
        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['localizacao'] ?? 'N/D') . "</td>";
     
        echo "<td>" . htmlspecialchars($row['quantidade']);
        echo ($tipo == 'livre') ? " (livres)" : " (em uso)";
        echo "</td>";
        
        echo "<td>" . htmlspecialchars($row['fabricante'] ?? 'Desconhecido') . "</td>";
        echo "<td>" . htmlspecialchars($row['responsavel'] ?? '-') . "</td>";
        echo "<td></td>"; 
        echo "</tr>";

        echo "<tr class='expand-row' data-id='" . $id_equip . "' hidden>
                <td colspan='6' class='expand-cell'>
                    <div class='row-actions'>";

        if ($is_admin) {
    
            echo "<button class='acao-btn editar' data-id='" . $id_equip . "'>Editar Equipamento</button>";
            echo "<button class='acao-btn trocar-prat' data-id='" . $id_equip . "'>Trocar Prateleira</button>";
        
        } else {
      
            if ($tipo == 'livre') {
                $max_qty = htmlspecialchars($row['quantidade'] ?? '0');
                echo "<input type='number' class='quantidade' placeholder='Quantidade' min='1' max='" . $max_qty . "'>";
                echo "<button class='acao-btn pegar'>Pegar Equipamento</button>";
                echo "<button class='acao-btn devolver'>Devolver Equipamento</button>";
            } else {
                echo "<span>Item em uso por: " . htmlspecialchars($row['responsavel']) . ".</span>";
            }
        }

        echo "      </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>Nenhum equipamento encontrado para este filtro.</td></tr>";
}

$conn->close();
?>