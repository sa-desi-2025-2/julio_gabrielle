<?php

include '../php/painel_admin.php'; 
include '../php/conexao.php';

$id_funcionario = (int)($_GET['id'] ?? 0);
$id_admin_logado = $_SESSION['id_funcionario'] ?? 0; 
$funcionario = null;
$erro = '';

if ($id_funcionario > 0) {
  
    $stmt = $conn->prepare("SELECT nome, cargo, RE, adm FROM funcionarios WHERE id_funcionario = ?");
    $stmt->bind_param("i", $id_funcionario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $funcionario = $result->fetch_assoc();
    } else {
        $erro = "Funcionário não encontrado.";
    }
    $stmt->close();
} else {
    $erro = "Nenhum ID de funcionário fornecido.";
}

$conn->close();

$view_atual = 'funcionarios'; 

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Funcionário</title>
  <link rel="stylesheet" href="../css/telaPrincipal.css" />
</head>
<body>
  <div class="dashboard">
    <aside class="sidebar">
      <h2 class="logo">Almoxarifado</h2>
      <ul class="menu">
        
        <li>
            <a href="telaeditor.php?view=todos" class="<?php echo ($view_atual == 'todos') ? 'ativo' : ''; ?>">
             Todos Equipamentos
            </a>
        </li>
        <li>
            <a href="telaeditor.php?view=livres" class="<?php echo ($view_atual == 'livres') ? 'ativo' : ''; ?>">
             Equipamentos Livres
            </a>
        </li>
        <li>
            <a href="telaeditor.php?view=ocupados" class="<?php echo ($view_atual == 'ocupados') ? 'ativo' : ''; ?>">
             Equipamentos Ocupados
            </a>
        </li>
        <li>
            <a href="telaeditor.php?view=meus" class="<?php echo ($view_atual == 'meus') ? 'ativo' : ''; ?>">
             Meus Equipamentos
            </a>
        </li>
        
        <li>
          <a href="telaeditor.php?view=funcionarios" class="<?php echo ($view_atual == 'funcionarios') ? 'ativo' : ''; ?>">
            Funcionários
          </a>
        </li>

        <li>
          <a href="telaeditor.php?view=movimentacoes" class="<?php echo ($view_atual == 'movimentacoes') ? 'ativo' : ''; ?>">
            Movimentações
          </a>
        </li>

        <li class="usuario-logado">
          <span class="nome-usuario">
            <?php echo htmlspecialchars($_SESSION['nome'] ??  'Usuário'); ?>
          </span>
        </li>
        <li>
          <a href="../html/entrarNaConta.html">Sair</a>
        </li>
      </ul>
    </aside>
    <main class="content">
      <h1>Editar Funcionário</h1>
      <div class="breadcrumbs">
        <span>Administração</span> > <a href="telaeditor.php?view=funcionarios">Funcionários</a> > <span class="atual">Editar</span>
      </div>

      <div class="form-container">
        <?php if ($erro): ?>
          <p class="erro"><?php echo $erro; ?> <a href="telaeditor.php?view=funcionarios">Voltar</a></p>
        <?php elseif ($funcionario): ?>
          
          <form class="form-editar" action="../php/atualizar_funcionario.php" method="POST">
            
            <input type="hidden" name="id_funcionario" value="<?php echo $id_funcionario; ?>">

            <div class="form-grupo">
              <label>Nome do Funcionário</label>
              <input type="text" name="nome" required value="<?php echo htmlspecialchars($funcionario['nome']); ?>" />
            </div>
            
            <div class="form-grupo">
              <label>Cargo</label>
              <input type="text" name="cargo" required value="<?php echo htmlspecialchars($funcionario['cargo']); ?>" />
            </div>

            <div class="form-grupo">
              <label>RE (Não editável)</label>
              <input type="text" name="re" readonly disabled value="<?php echo htmlspecialchars($funcionario['RE']); ?>" style="background:#eee;" />
            </div>
            
            <div class="form-grupo checkbox-grupo">
              <label>
                <input 
                  type="checkbox" 
                  name="administrador" 
                  <?php echo ($funcionario['adm'] == 1) ? 'checked' : '';  ?>
                  <?php echo ($id_funcionario == $id_admin_logado) ? 'disabled' : '';  ?>
                >
                Administrador
              </label>
              
              <?php if ($id_funcionario == $id_admin_logado):  ?>
                <small style="margin-left: 10px; color: #777;">(Você não pode editar seu próprio status de admin)</small>
              <?php endif; ?>
            </div>


            <div class="botoes">
              <button type="submit" class="salvar">Salvar Alterações</button>
              <button type="button" class="cancelar" onclick="window.location.href='telaeditor.php?view=funcionarios'">Cancelar</button>
            </div>
          </form>
          
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>