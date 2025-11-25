<?php
include '../php/painel_usuario.php';

$view_atual = $_GET['view'] ?? 'todos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel do Usuário</title>
  <link rel="stylesheet" href="../css/telaPrincipal.css">
  

</head>
<body>
  <div class="dashboard">
  <aside class="sidebar">
  <h2 class="logo">Almoxarifado</h2>
  <ul class="menu">
    <li>
      <a href="telaPrincipal.php?view=todos" class="<?php echo ($view_atual == 'todos') ? 'ativo' : ''; ?>">
        Início
      </a>
    </li>
    <li>
      <a href="telaPrincipal.php?view=livres" class="<?php echo ($view_atual == 'livres') ? 'ativo' : ''; ?>">
        Equipamentos livres
      </a>
    </li>
    <li>
      <a href="telaPrincipal.php?view=ocupados" class="<?php echo ($view_atual == 'ocupados') ? 'ativo' : ''; ?>">
        Equipamentos ocupados
      </a>
    </li>
    <li>
      <a href="telaPrincipal.php?view=meus" class="<?php echo ($view_atual == 'meus') ? 'ativo' : ''; ?>">
        Meus equipamentos
      </a>
    </li>

    <li class="usuario-logado">
      <span class="nome-usuario">
        <?php echo htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>
      </span>
    </li>

    <li class="logout">
      <a href="../html/entrarNaConta.html">Sair</a>
    </li>
  </ul>
</aside>


    <main class="content">

      <header class="header">
          <h1>Equipamentos Ativos</h1>
          <form class="actions" method="GET" action="">
            
            <input type="hidden" name="view" value="<?php echo htmlspecialchars($view_atual); ?>">
            
            <input 
              type="text" 
              placeholder="Buscar por nome, marca, local..." 
              class="search" 
              name="busca"
              value="<?php echo htmlspecialchars($_GET['busca'] ?? ''); ?>"
            />
            <button type="submit" class="btn">Filtrar</button>
          </form>
        </header>
        

        <section class="table-section">
          <table class="table">
            <thead>
              <tr>
                <th>Nome do Equipamento</th>
                <th>Localização</th>
                <th>Descrição</th> <th>Quantidade</th>
                <th>Marca</th>
                <th>Responsável</th>
                <th></th> 
              </tr>
            </thead>
            <tbody>
              <?php include '../php/equipamentos.php'; ?>
            </tbody>
          </table>
        </section>

    </main>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
    
      const linhas = document.querySelectorAll('.data-row');
      linhas.forEach(linha => {
        linha.addEventListener('click', (evento) => {
          const linhaExpandida = linha.nextElementSibling;
         
          if (evento.target.closest('button') || evento.target.closest('input')) return; 

          const expandido = linha.getAttribute('aria-expanded') === 'true';
          linha.setAttribute('aria-expanded', !expandido);
          
          linhaExpandida.hidden = expandido; 
        });
      });

      document.addEventListener('click', (e) => {
        const botao = e.target;

        if (botao.classList.contains('pegar') || botao.classList.contains('devolver')) {
          const acao = botao.classList.contains('pegar') ? 'pegar_equipamentos' : 'devolver_equipamento';
          
          const linhaExpandida = botao.closest('.expand-row'); 
          
          const id_equipamento = linhaExpandida.dataset.id;
          const inputQtd = linhaExpandida.querySelector('.quantidade');
          
          if (!inputQtd) return; 
          
          const quantidade = inputQtd.value; 

          if (!quantidade || quantidade <= 0) {
            alert('Informe uma quantidade válida.');
            return;
          }
          const observacao = prompt('Digite uma observação (opcional):', '');
         fetch(`../php/${acao}.php`, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id_equipamento=${encodeURIComponent(id_equipamento)}&quantidade=${encodeURIComponent(quantidade)}&observacao=${encodeURIComponent(observacao)}`
          })
          .then(res => res.text())
          .then(mensagem => {
            alert(mensagem);
            location.reload(); 
          })
          .catch(() => alert('Erro ao comunicar com o servidor.'));
        }
      });
    });
  </script>
</body>
</html>