<?php
include '../php/painel_admin.php';
include '../php/conexao.php';

$id_prateleira = (int)($_GET['id'] ?? 0);
$prateleira = null;
$erro = '';

if ($id_prateleira > 0) {
    $stmt = $conn->prepare("SELECT numero_prateleira FROM prateleiras WHERE id_prateleira = ?");
    $stmt->bind_param("i", $id_prateleira);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $prateleira = $result->fetch_assoc();
    } else {
        $erro = "Prateleira não encontrada.";
    }
    $stmt->close();
} else {
    $erro = "ID inválido.";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Prateleira</title>
  <link rel="stylesheet" href="../css/telaPrincipal.css" />
</head>
<body>
  <div class="dashboard">
    <aside class="sidebar">
      <h2 class="logo">Almoxarifado</h2>
      <ul class="menu">
        <li class="ativo">Voltar</li>
      </ul>
    </aside>

    <main class="content">
      <h1>Editar Prateleira</h1>
      <div class="breadcrumbs">
        <span>Configurações</span> > <a href="telaeditor.php?view=prateleiras">Prateleiras</a> > <span class="atual">Editar</span>
      </div>

      <div class="form-container">
        <?php if ($erro): ?>
          <p class="erro" style="color:red;"><?php echo $erro; ?> <a href="telaeditor.php?view=prateleiras">Voltar</a></p>
        <?php elseif ($prateleira): ?>
          
          <form class="form-editar" action="../php/salvar_edicao_prateleira.php" method="POST">
            <input type="hidden" name="id_prateleira" value="<?php echo $id_prateleira; ?>">

            <div class="form-grupo">
              <label>Nome/Número da Prateleira</label>
              <input type="text" name="numero_prateleira" required value="<?php echo htmlspecialchars($prateleira['numero_prateleira']); ?>" />
            </div>

            <div class="botoes">
              <button type="submit" class="salvar">Salvar Alterações</button>
              <button type="button" class="cancelar" onclick="window.location.href='telaeditor.php?view=prateleiras'">Cancelar</button>
            </div>
          </form>

        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>