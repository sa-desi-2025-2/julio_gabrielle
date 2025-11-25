<?php
include 'conexao.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$eh_admin = (isset($_SESSION['adm']) && $_SESSION['adm'] == 1);
$id_usuario_logado = $_SESSION['id_funcionario'] ?? 0; 
$modo_visualizacao = $_GET['view'] ?? 'todos';


$lista_prateleiras = [];
if ($eh_admin) { 
    $sql_prateleiras = "SELECT id_prateleira, numero_prateleira FROM prateleiras ORDER BY numero_prateleira ASC";
    $resultado_prateleiras = $conn->query($sql_prateleiras);
    if ($resultado_prateleiras && $resultado_prateleiras->num_rows > 0) {
        while($linha_prat = $resultado_prateleiras->fetch_assoc()) {
            $lista_prateleiras[] = $linha_prat;
        }
    }
}



$termo_busca = null;
$parametro_busca = null;
$parametros = [];
$tipos = "";

if (isset($_GET['busca']) && !empty(trim($_GET['busca']))) {
    $termo_busca = trim($_GET['busca']);
    $parametro_busca = "%" . $termo_busca . "%"; 
}

$sql_livres = "
    SELECT 
        e.id_equipamento,
        e.nome,
        e.fabricante,
        e.quantidade,
        e.descricao,
        p.numero_prateleira AS localizacao,
        '-' AS responsavel,
        0 AS id_responsavel, 
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
        e.descricao,
        p.numero_prateleira AS localizacao,
        f.nome AS responsavel,
        f.id_funcionario AS id_responsavel, 
        'ocupado' AS tipo_item
    FROM movimentacoes m
    JOIN equipamentos e ON m.id_equipamento = e.id_equipamento
    JOIN funcionarios f ON m.id_funcionario = f.id_funcionario
    LEFT JOIN prateleiras p ON e.id_prateleira = p.id_prateleira
    GROUP BY m.id_equipamento, m.id_funcionario, f.nome, e.nome, e.fabricante, e.descricao, p.numero_prateleira
    HAVING quantidade > 0
";

$sql_meus = "
    SELECT 
        e.id_equipamento,
        e.nome,
        e.fabricante,
        (SUM(CASE WHEN m.tipo_movimentacao = 'saida' THEN m.quantidade ELSE 0 END) - 
         SUM(CASE WHEN m.tipo_movimentacao = 'entrada' THEN m.quantidade ELSE 0 END)) AS quantidade,
        e.descricao, /* ADICIONADO AQUI */
        p.numero_prateleira AS localizacao,
        f.nome AS responsavel,
        f.id_funcionario AS id_responsavel,
        'ocupado' AS tipo_item
    FROM movimentacoes m
    JOIN equipamentos e ON m.id_equipamento = e.id_equipamento
    JOIN funcionarios f ON m.id_funcionario = f.id_funcionario
    LEFT JOIN prateleiras p ON e.id_prateleira = p.id_prateleira
    WHERE m.id_funcionario = ? -- Filtro pelo usuário logado
    GROUP BY m.id_equipamento, m.id_funcionario, f.nome, e.nome, e.fabricante, e.descricao, p.numero_prateleira
    HAVING quantidade > 0
";


$sql_filtrar = "
AND (e.nome LIKE ? OR e.fabricante LIKE ? OR p.numero_prateleira LIKE ? OR e.descricao LIKE ?)
WHERE (e.nome LIKE ? OR e.fabricante LIKE ? OR p.numero_prateleira LIKE ? OR f.nome LIKE ? OR e.descricao LIKE ?)
";


if ($modo_visualizacao == 'livres') {
    $parametros = [];
    $tipos = "";
    if ($termo_busca) {
        $sql_livres .= " AND (e.nome LIKE ? OR e.fabricante LIKE ? OR p.numero_prateleira LIKE ? OR e.descricao LIKE ?) "; 
        $parametros = [$parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca];
        $tipos = "ssss";
    }
    $sql = $sql_livres . " ORDER BY e.nome ASC";
    $declaracao = $conn->prepare($sql);
    if ($termo_busca) {
        $declaracao->bind_param($tipos, ...$parametros);
    }

} else if ($modo_visualizacao == 'ocupados') {
    $parametros = [];
    $tipos = "";
    if ($termo_busca) {
        
        $sql_ocupados = preg_replace('/(FROM .*?)(GROUP BY)/s', '$1 WHERE (e.nome LIKE ? OR e.fabricante LIKE ? OR p.numero_prateleira LIKE ? OR f.nome LIKE ? OR e.descricao LIKE ?) $2', $sql_ocupados, 1);
        $parametros = [$parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca];
        $tipos = "sssss";
    }
    $sql = $sql_ocupados . " ORDER BY responsavel ASC, nome ASC";
    $declaracao = $conn->prepare($sql);
    if ($termo_busca) {
        $declaracao->bind_param($tipos, ...$parametros);
    }

} else if ($modo_visualizacao == 'meus') {
    if ($id_usuario_logado == 0) {
        $resultado = false;
        $declaracao = null; 
    } else {
        $parametros = [$id_usuario_logado];
        $tipos = "i";
        if ($termo_busca) {
            $sql_meus .= " AND (e.nome LIKE ? OR e.fabricante LIKE ? OR p.numero_prateleira LIKE ? OR f.nome LIKE ? OR e.descricao LIKE ?) "; // Busca por descrição
            $parametros = array_merge($parametros, [$parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca]);
            $tipos .= "sssss";
        }
        $sql = $sql_meus . " ORDER BY nome ASC";
        $declaracao = $conn->prepare($sql);
        $declaracao->bind_param($tipos, ...$parametros);
    }
} else { 
    $parametros = [];
    $tipos = "";
    if ($termo_busca) {
       
        $sql_livres .= " AND (e.nome LIKE ? OR e.fabricante LIKE ? OR p.numero_prateleira LIKE ? OR '-' LIKE ? OR e.descricao LIKE ?) ";
        $parametros = array_merge($parametros, [$parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca]);
        $tipos .= "sssss";
        
     
        $sql_ocupados = preg_replace('/(FROM .*?)(GROUP BY)/s', '$1 WHERE (e.nome LIKE ? OR e.fabricante LIKE ? OR p.numero_prateleira LIKE ? OR f.nome LIKE ? OR e.descricao LIKE ?) $2', $sql_ocupados, 1);
        $parametros = array_merge($parametros, [$parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca, $parametro_busca]);
        $tipos .= "sssss";
    }
    
    $sql = $sql_livres . " UNION ALL " . $sql_ocupados . " ORDER BY nome ASC, responsavel ASC";
    $declaracao = $conn->prepare($sql);
    if ($termo_busca) {
        $declaracao->bind_param($tipos, ...$parametros);
    }
}


if ($declaracao) {
    $declaracao->execute();
    $resultado = $declaracao->get_result();
}



if ($resultado && $resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $tipo = $linha['tipo_item'];
        $id_equip = htmlspecialchars($linha['id_equipamento']);
        
        $id_responsavel_item = $linha['id_responsavel'] ?? 0; 
       
        echo "<tr class='data-row' data-id='" . $id_equip . "' tabindex='0' aria-expanded='false'>";
        echo "<td>" . htmlspecialchars($linha['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($linha['localizacao'] ?? 'N/D') . "</td>";
        
       
        echo "<td>" . htmlspecialchars($linha['descricao'] ?? 'N/D') . "</td>";
     
        echo "<td>" . htmlspecialchars($linha['quantidade']);
        echo ($tipo == 'livre') ? " (livres)" : " (em uso)";
        echo "</td>";
        
        echo "<td>" . htmlspecialchars($linha['fabricante'] ?? 'Desconhecido') . "</td>";
        echo "<td>" . htmlspecialchars($linha['responsavel'] ?? '-') . "</td>";
        echo "<td></td>"; 
        echo "</tr>";

       
        
        echo "<tr class='expand-row' data-id='" . $id_equip . "' hidden>
                <td colspan='7' class='expand-cell'> 
                    <div class='row-actions'>";

    
        if ($eh_admin) {
            echo "<button class='acao-btn editar' data-id='" . $id_equip . "'>Editar Equipamento</button>";
            
            echo "<div class='dropdown-prat-container'>";
            echo "<button class='acao-btn trocar-prat' data-id='" . $id_equip . "'>Trocar Prateleira</button>";
            
            echo "<ul class='dropdown-menu-prat' data-id-equip='" . $id_equip . "'>";
            
            if (empty($lista_prateleiras)) {
                echo "<li class='dropdown-item-prat disabled'>Nenhuma prateleira cadastrada</li>";
            } else {
                foreach ($lista_prateleiras as $prat) {
                    echo "<li class='dropdown-item-prat' data-id-prat='" . htmlspecialchars($prat['id_prateleira']) . "'>";
                    echo htmlspecialchars($prat['numero_prateleira']);
                    echo "</li>";
                }
            }
            
            echo "</ul>";
            echo "</div>"; 
        } 
        
       
        if ($tipo == 'livre') {
            
            $qtd_maxima = htmlspecialchars($linha['quantidade'] ?? '0');
            echo "<input type='number' class='quantidade' placeholder='Qtd' min='1' max='" . $qtd_maxima . "'>";
            echo "<button class='acao-btn pegar'>Pegar Equipamento</button>";
        
        } else if ($tipo == 'ocupado' && $id_responsavel_item == $id_usuario_logado) {
            
            $qtd_maxima = htmlspecialchars($linha['quantidade'] ?? '0'); 
            echo "<input type='number' class='quantidade' placeholder='Qtd' min='1' max='" . $qtd_maxima . "'>";
            echo "<button class='acao-btn devolver'>Devolver Equipamento</button>";
            
        } else if ($tipo == 'ocupado' && $id_responsavel_item != $id_usuario_logado) {
          
            echo "<span>Item em uso por: " . htmlspecialchars($linha['responsavel']) . ".</span>";
        }
        

        echo "      </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>Nenhum equipamento encontrado para este filtro.</td></tr>"; // Colspan atualizado
}


if (isset($declaracao)) {
    $declaracao->close();
}
$conn->close();
?>