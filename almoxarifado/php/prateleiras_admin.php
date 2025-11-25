<?php
include_once 'conexao.php';

$sql = "SELECT id_prateleira, numero_prateleira FROM prateleiras ORDER BY numero_prateleira ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id_prateleira']);
        $nome = htmlspecialchars($row['numero_prateleira']);
        
        echo "<tr>";
        echo "<td>" . $id . "</td>";
        echo "<td>" . $nome . "</td>";
        echo "<td>";
     
        echo "<button class='acao-btn editar-prat' data-id='" . $id . "' style='margin-right: 5px; background-color:#1aa0c2; color:white;'>Editar</button>";
        
      
        echo "<button class='acao-btn devolver excluir-prat' data-id='" . $id . "' style='background-color:#e73c3c; color:white;'>Excluir</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>Nenhuma prateleira cadastrada.</td></tr>";
}

?>