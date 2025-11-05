<?php
include '../php/painel_admin.php';

$view_atual = $_GET['view'] ?? 'todos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel do Administrador</title>
  <link rel="stylesheet" href="../css/telaPrincipal.css" />

</head>
<body>
  <div class="dashboard">
    <aside class="sidebar">
      <h2 class="logo">Almoxarifado</h2>
      <ul class="menu">
        <li>
          <a href="telaeditor.php?view=todos" class="<?php echo ($view_atual == 'todos') ? 'ativo' : ''; ?>">
            Início
          </a>
        </li>
        <li>
          <a href="telaeditor.php?view=livres" class="<?php echo ($view_atual == 'livres') ? 'ativo' : ''; ?>">
            Equipamentos livres
          </a>
        </li>
        <li>
          <a href="telaeditor.php?view=ocupados" class="<?php echo ($view_atual == 'ocupados') ? 'ativo' : ''; ?>">
            Equipamentos ocupados
          </a>
        </li>
        <li>
        <a href="telaeditor.php?view=meus" class="<?php echo ($view_atual == 'meus') ? 'ativo' : ''; ?>">
          Meus equipamentos
        </a>
        </li>
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
              <th>Quantidade</th>
              <th>Marca</th>
              <th>Responsável</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
              <?php include '../php/equipamentos.php';  ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <div class="speed-dial" id="speedDial"> 
    <div class="speed-dial-actions" id="speedActions" aria-hidden="true">
      <button class="sd-btn" data-action="adicionar-usuario" type="button">Adicionar usuário</button>
      <button class="sd-btn" data-action="adicionar-equipamento" type="button">Adicionar equipamento</button>
    </div>
    <button class="fab" id="fab" aria-expanded="false" aria-label="Abrir opções">+</button>
  </div>


    <script>
      
      document.addEventListener('DOMContentLoaded', () => {
        
        
        const fab = document.getElementById('fab');
        const speedDial = document.getElementById('speedDial');
        const actions = document.getElementById('speedActions');
    
        const open = () => {
          speedDial.classList.add('open');
          fab.classList.add('open');
          actions.setAttribute('aria-hidden', 'false');
          fab.setAttribute('aria-expanded', 'true');
        };
    
        const close = () => {
          speedDial.classList.remove('open');
          fab.classList.remove('open');
          actions.setAttribute('aria-hidden', 'true');
          fab.setAttribute('aria-expanded', 'false');
        };
    
        fab.addEventListener('click', (e) => {
          e.stopPropagation();
          speedDial.classList.contains('open') ? close() : open();
        });
    
        
        document.addEventListener('click', (e) => {
         
          if (speedDial && !speedDial.contains(e.target)) close();

          if (!e.target.closest('.dropdown-prat-container')) {
            document.querySelectorAll('.dropdown-prat-container.open').forEach(container => {
              container.classList.remove('open');
            });
          }
        });
       
    
     
        document.querySelectorAll('.sd-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const action = btn.dataset.action;
    
            if (action === 'adicionar-usuario') {
              window.location.href = '../html/registra_funcionario.html';
            } 
            else if (action === 'adicionar-equipamento') {
              window.location.href = '../html/adicionarEquipamento.html';
            }
          });
        });

   
        document.querySelectorAll('.data-row').forEach(row => {
          row.addEventListener('click', (event) => {
           
            if (event.target.closest('button') || event.target.closest('input')) return; 

            const expandRow = row.nextElementSibling;
            const expanded = row.getAttribute('aria-expanded') === 'true';
            
            row.setAttribute('aria-expanded', !expanded);
            expandRow.hidden = expanded; 
          });
        });

      
        document.querySelector('.table-section').addEventListener('click', (e) => {
          const btn = e.target;

          
          if (btn.classList.contains('editar')) {
            const id = btn.dataset.id;
            alert(`Redirecionando para editar o item ID: ${id}\n(Criar página: editarEquipamento.php?id=${id})`);
          }

         
          
          
          if (btn.classList.contains('trocar-prat')) {
            e.stopPropagation(); 
            const container = btn.closest('.dropdown-prat-container');

           
            const isOpen = container.classList.contains('open');
            
           
            document.querySelectorAll('.dropdown-prat-container.open').forEach(openContainer => {
                openContainer.classList.remove('open');
            });

            
            if (!isOpen) {
                container.classList.add('open');
            }
           
          }

         
          if (btn.classList.contains('dropdown-item-prat')) {
            const idPrateleira = btn.dataset.idPrat;
            if (!idPrateleira) {
                alert('Esta não é uma prateleira válida.');
                return; 
            }
            
            const menu = btn.closest('.dropdown-menu-prat');
            const idEquipamento = menu.dataset.idEquip;
            const nomePrateleira = btn.textContent.trim();

            if (confirm(`Mover equipamento (ID: ${idEquipamento}) para a prateleira "${nomePrateleira}"?`)) {
                
               
                fetch('../php/atualizar_prateleira.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id_equipamento=${encodeURIComponent(idEquipamento)}&id_prateleira=${encodeURIComponent(idPrateleira)}`
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg); 
                    if (msg.includes('sucesso')) {
                        location.reload(); 
                    }
                })
                .catch(() => alert('Erro ao comunicar com o servidor.'));
            }
          }

         

         
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