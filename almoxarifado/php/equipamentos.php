<?php
include 'conexao.php';

$sql = "
    SELECT 
        e.id_equipamento,
        e.nome,
        e.fabricante,
        e.quantidade,
        p.numero_prateleira AS localizacao
    FROM equipamentos e
    LEFT JOIN prateleiras p ON e.id_prateleira = p.id_prateleira
    ORDER BY e.nome ASC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    
        echo "<tr class='data-row' data-id='" . htmlspecialchars($row['id_equipamento']) . "' tabindex='0' aria-expanded='false'>";
        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['localizacao'] ?? 'N/D') . "</td>";
        echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fabricante'] ?? 'Desconhecido') . "</td>";
        echo "<td>-</td>"; 
        echo "</tr>";

        echo "<tr class='expand-row' data-id='" . htmlspecialchars($row['id_equipamento']) . "' hidden>
                <td colspan='6' class='expand-cell'>
                    <div class='row-actions'>
                        <input type='number' class='quantidade' placeholder='Quantidade' min='1' max='" . htmlspecialchars($row['quantidade']) . "'>
                        <button class='acao-btn pegar'>Pegar Equipamento</button>
                        <button class='acao-btn devolver'>Devolver Equipamento</button>
                    </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>Nenhum equipamento encontrado.</td></tr>";
}

$conn->close();
?>