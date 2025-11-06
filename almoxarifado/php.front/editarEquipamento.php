<?php

include '../php/painel_admin.php';
include '../php/conexao.php';

$id_equipamento = (int)($_GET['id'] ?? 0);
$equipamento = null;
$erro = '';

if ($id_equipamento > 0) {
    
    $stmt = $conn->prepare("SELECT nome, fabricante, quantidade, descricao FROM equipamentos WHERE id_equipamento = ?");
    $stmt->bind_param("i", $id_equipamento);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $equipamento = $result->fetch_assoc();
    } else {
        $erro = "Equipamento não encontrado.";
    }
    $stmt->close();
} else {
    $erro = "Nenhum ID de equipamento fornecido.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Equipamento</title>
  <link rel="stylesheet" href="../css/telaPrincipal.css" />
</head>
<body>
  <div class="dashboard">
    <aside class="sidebar">
      <h2 class="logo">Almoxarifado</h2>
      <ul class="menu">
        <li><a href="telaeditor.php">Início</a></li>
      </ul>
    </aside>

    <main class="content">
      <h1>Editar Equipamento</h1>
      <div class="breadcrumbs">
        <span>Equipamentos</span> > <span class="atual">Editar Equipamento</span>
      </div>

      <div class="form-container">
        <?php if ($erro): ?>
          <p class="erro"><?php echo $erro; ?> <a href="telaeditor.php">Voltar</a></p>
        <?php elseif ($equipamento): ?>
          <form class="form-editar" action="../php/atualizar_equipamento.php" method="POST">
            
            <input type="hidden" name="id_equipamento" value="<?php echo $id_equipamento; ?>">

            <div class="form-grupo">
              <label>Nome do Equipamento</label>
              <input type="text" name="nome" required value="<?php echo htmlspecialchars($equipamento['nome']); ?>" />
            </div>

            <div class="form-grupo">
              <label>Quantidade (em estoque)</label>
              <input type="number" name="quantidade" min="0" required value="<?php echo htmlspecialchars($equipamento['quantidade']); ?>" />
            </div>

            <div class="form-grupo">
              <label>Marca</label>
              <input type="text" name="fabricante" value="<?php echo htmlspecialchars($equipamento['fabricante']); ?>" />
            </div>

            <div class="form-grupo">
              <label>Descrição</label>
              <input type="text" name="descricao" placeholder="Digite uma descrição..." value="<?php echo htmlspecialchars($equipamento['descricao']); ?>" />
            </div>

            <div class="botoes">
              <button type="submit" class="salvar">Salvar Alterações</button>
              <button type="button" class="cancelar" onclick="window.location.href='telaeditor.php'">Cancelar</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>