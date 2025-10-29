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
        <li class="ativo">Início</li>
        <li>Equipamentos</li>
        <li>Ferramentas</li>
        <li>Configurações</li>
      </ul>
    </aside>

    <main class="content">
      <header class="header">
        <h1>Equipamentos Ativos</h1>
        <div class="actions">
          <input type="text" placeholder="Buscar equipamento..." class="search">
          <button class="btn">Filtrar</button>
        </div>
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
            </tr>
          </thead>
          <tbody>
            <?php include '../php/telaPrincipal.php'; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>

    document.addEventListener('DOMContentLoaded', () => {
      const rows = document.querySelectorAll('.data-row');
      rows.forEach(row => {
        row.addEventListener('click', () => {
          const expandRow = row.nextElementSibling;
          const expanded = row.getAttribute('aria-expanded') === 'true';
          row.setAttribute('aria-expanded', !expanded);
          expandRow.hidden = expanded;
        });
      });
    });
  </script>
</body>
</html>
