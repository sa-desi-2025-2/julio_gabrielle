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
  <style>
    .sidebar ul li a { display: block; text-decoration: none; color: inherit; }
    .sidebar ul li a.ativo { background-color: #1aa0c2; border-radius: 8px; }
  </style>
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
    </aside>

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
              <th>Quantidade</th>
              <th>Marca</th>
              <th>Responsável</th>
              <th>Ações</th> 
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
    
      const rows = document.querySelectorAll('.data-row');
      rows.forEach(row => {
        row.addEventListener('click', (event) => {
          const expandRow = row.nextElementSibling;
         
          if (event.target.closest('button') || event.target.closest('input')) return; 

          const expanded = row.getAttribute('aria-expanded') === 'true';
          row.setAttribute('aria-expanded', !expanded);
          
          expandRow.hidden = expanded; 
        });
      });

      document.addEventListener('click', (e) => {
        const btn = e.target;

        if (btn.classList.contains('pegar') || btn.classList.contains('devolver')) {
          const action = btn.classList.contains('pegar') ? 'pegar_equipamentos' : 'devolver_equipamento';
          
          const expandRow = btn.closest('.expand-row'); 
          
          const id_equipamento = expandRow.dataset.id;
          const inputQtd = expandRow.querySelector('.quantidade');
          
          if (!inputQtd) return; 
          
          const quantidade = inputQtd.value; 

          if (!quantidade || quantidade <= 0) {
            alert('Informe uma quantidade válida.');
            return;
          }

          fetch(`../php/${action}.php`, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id_equipamento=${encodeURIComponent(id_equipamento)}&quantidade=${encodeURIComponent(quantidade)}`
          })
          .then(res => res.text())
          .then(msg => {
            alert(msg);
            location.reload(); 
          })
          .catch(() => alert('Erro ao comunicar com o servidor.'));
        }
      });
    });
  </script>
</body>
</html>