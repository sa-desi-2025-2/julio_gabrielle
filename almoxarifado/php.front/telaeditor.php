<?php
include '../php/painel_admin.php';

$view_atual = $_GET['view'] ?? 'todos';

$titulo_pagina = "Equipamentos Ativos";
$placeholder_busca = "Buscar por nome, marca, local, descrição...";


if ($view_atual == 'funcionarios') {
    $titulo_pagina = "Gerenciar Funcionários";
    $placeholder_busca = "Buscar por nome, cargo ou RE...";
} elseif ($view_atual == 'movimentacoes') {
    $titulo_pagina = "Histórico de Movimentações";
    $placeholder_busca = "Buscar por equipamento, funcionário ou obs...";
} elseif ($view_atual == 'prateleiras') {
    $titulo_pagina = "Gerenciar Prateleiras";
    $placeholder_busca = "Busca desativada para esta tela";
} elseif ($view_atual == 'livres') {
    $titulo_pagina = "Equipamentos Livres";
} elseif ($view_atual == 'ocupados') {
    $titulo_pagina = "Equipamentos Ocupados";
} elseif ($view_atual == 'meus') {
    $titulo_pagina = "Meus Equipamentos";
} elseif ($view_atual == 'todos') {
     $titulo_pagina = "Todos os Equipamentos";
}

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
            <a href="telaeditor.php?view=prateleiras" class="<?php echo ($view_atual == 'prateleiras') ? 'ativo' : ''; ?>">
             Prateleiras
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
    <header class="header">
        <h1><?php echo $titulo_pagina; ?></h1>
        
        <form class="actions" method="GET" action="">
          <input type="hidden" name="view" value="<?php echo htmlspecialchars($view_atual); ?>">
          
          <input 
            type="text" 
            placeholder="<?php echo $placeholder_busca;  ?>" 
            class="search" 
            name="busca"
            value="<?php echo htmlspecialchars($_GET['busca'] ?? ''); ?>"
            <?php echo ($view_atual == 'prateleiras') ? 'disabled' : ''; ?>
          />
          
          <?php if ($view_atual != 'prateleiras'): ?>
            <button type="submit" class="btn">Filtrar</button>
          <?php endif; ?>
        </form>
      </header>

      <section class="table-section">
        <table class="table">
          
          <?php if ($view_atual == 'funcionarios'): ?>
            <thead>
              <tr>
                <th>Nome do Funcionário</th>
                <th>Cargo</th>
                <th>RE</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
                <?php include '../php/funcionarios_admin.php';  ?>
            </tbody>

          <?php elseif ($view_atual == 'movimentacoes'): ?>
            <thead>
              <tr>
                <th>Data/Hora</th>
                <th>Equipamento</th>
                <th>Funcionário</th>
                <th>Tipo</th>
                <th>Qtd</th>
                <th>Observação</th>
              </tr>
            </thead>
            <tbody>
                <?php include '../php/movimentacoes_admin.php';  ?>
            </tbody>

          <?php elseif ($view_atual == 'prateleiras'): ?>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome/Número da Prateleira</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
                <?php include '../php/prateleiras_admin.php'; ?>
            </tbody>

          <?php else: ?>
            <thead>
              <tr>
                <th>Nome do Equipamento</th>
                <th>Localização</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Marca</th>
                <th>Responsável</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
                <?php include '../php/equipamentos.php';  ?>
            </tbody>
          <?php endif; ?>
          
        </table>
      </section>
    </main>
  </div>

  <?php if ($view_atual != 'funcionarios' && $view_atual != 'movimentacoes'): ?>
  <div class="speed-dial" id="speedDial"> 
    <div class="speed-dial-actions" id="speedActions" aria-hidden="true">
      <button class="sd-btn" data-action="adicionar-usuario" type="button">Adicionar usuário</button>
      <button class="sd-btn" data-action="adicionar-equipamento" type="button">Adicionar equipamento</button>
      <button class="sd-btn" data-action="adicionar-prateleira" type="button">Adicionar prateleira</button>
    </div>
    <button class="fab" id="fab" aria-expanded="false" aria-label="Abrir opções">+</button>
  </div>
  <?php endif; ?>


    <script>
      document.addEventListener('DOMContentLoaded', () => {
        
        const botaoFlutuante = document.getElementById('fab');
        if (botaoFlutuante) {
            const menuRapido = document.getElementById('speedDial');
            const acoesMenu = document.getElementById('speedActions');
        
            const abrir = () => {
              menuRapido.classList.add('open');
              botaoFlutuante.classList.add('open');
              acoesMenu.setAttribute('aria-hidden', 'false');
              botaoFlutuante.setAttribute('aria-expanded', 'true');
            };
        
            const fechar = () => {
              menuRapido.classList.remove('open');
              botaoFlutuante.classList.remove('open');
              acoesMenu.setAttribute('aria-hidden', 'true');
              botaoFlutuante.setAttribute('aria-expanded', 'false');
            };
        
            botaoFlutuante.addEventListener('click', (e) => {
              e.stopPropagation();
              menuRapido.classList.contains('open') ? fechar() : abrir();
            });
            
            document.addEventListener('click', (e) => {
                if (menuRapido && !menuRapido.contains(e.target)) fechar();
            });

            document.querySelectorAll('.sd-btn').forEach(botao => {
              botao.addEventListener('click', () => {
                const acao = botao.dataset.action;
                if (acao === 'adicionar-usuario') {
                  window.location.href = '../html/registra_funcionario.html';
                } 
                else if (acao === 'adicionar-equipamento') {
                  window.location.href = '../html/adicionarEquipamento.html';
                }
                else if (acao === 'adicionar-prateleira') {
                  window.location.href = '../html/adicionarPrateleira.html';
                }
              });
            });
        }
    
        document.querySelectorAll('.data-row').forEach(linha => {
          linha.addEventListener('click', (evento) => {
            if (evento.target.closest('button') || evento.target.closest('input')) return; 
            const linhaExpandida = linha.nextElementSibling;
            const expandido = linha.getAttribute('aria-expanded') === 'true';
            linha.setAttribute('aria-expanded', !expandido);
            linhaExpandida.hidden = expandido; 
          });
        });
        
        document.querySelector('.table-section').addEventListener('click', (e) => {
          const botao = e.target;

          if (botao.classList.contains('editar')) {
            const id = botao.dataset.id;
            window.location.href = `editarEquipamento.php?id=${id}`;
          }

          if (botao.classList.contains('editar-prat')) {
             const id = botao.dataset.id;
             window.location.href = `editarPrateleira.php?id=${id}`;
          }

          if (botao.classList.contains('excluir-prat')) {
            const id = botao.dataset.id;
            if (confirm('Tem certeza que deseja excluir esta prateleira? Os equipamentos nela ficarão sem local definido.')) {
                fetch('../php/excluir_prateleira.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id_prateleira=${encodeURIComponent(id)}`
                })
                .then(res => res.text())
                .then(mensagem => {
                    alert(mensagem);
                    location.reload();
                })
                .catch(() => alert('Erro ao comunicar com o servidor.'));
            }
          }

          if (botao.classList.contains('trocar-prat')) {
            e.stopPropagation(); 
            const recipiente = botao.closest('.dropdown-prat-container');
            const estaAberto = recipiente.classList.contains('open');
            document.querySelectorAll('.dropdown-prat-container.open').forEach(recipienteAberto => {
                recipienteAberto.classList.remove('open');
            });
            if (!estaAberto) {
                recipiente.classList.add('open');
            }
          }
          
          if (botao.classList.contains('dropdown-item-prat')) {
            const idPrateleira = botao.dataset.idPrat;
            if (!idPrateleira) {
                alert('Esta não é uma prateleira válida.');
                return; 
            }
            const menu = botao.closest('.dropdown-menu-prat');
            const idEquipamento = menu.dataset.idEquip;
            const nomePrateleira = botao.textContent.trim();

            if (confirm(`Mover equipamento (ID: ${idEquipamento}) para a prateleira "${nomePrateleira}"?`)) {
                fetch('../php/atualizar_prateleira.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id_equipamento=${encodeURIComponent(idEquipamento)}&id_prateleira=${encodeURIComponent(idPrateleira)}`
                })
                .then(res => res.text())
                .then(mensagem => {
                    alert(mensagem); 
                    if (mensagem.includes('sucesso')) {
                        location.reload(); 
                    }
                })
                .catch(() => alert('Erro ao comunicar com o servidor.'));
            }
          }

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

          if (botao.classList.contains('editar-func')) {
            const id = botao.dataset.id;
            window.location.href = `editarFuncionario.php?id=${id}`;
          }

          if (botao.classList.contains('desativar-func')) {
            const id = botao.dataset.id;
            if (confirm(`Tem certeza que deseja DESATIVAR este funcionário (ID: ${id})? Ele não poderá mais fazer login.`)) {
              enviarAtualizacaoStatus(id, 0); 
            }
          }

          if (botao.classList.contains('ativar-func')) {
            const id = botao.dataset.id;
            if (confirm(`Tem certeza que deseja REATIVAR este funcionário (ID: ${id})?`)) {
              enviarAtualizacaoStatus(id, 1); 
            }
          }
        
        });
        
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown-prat-container')) {
                document.querySelectorAll('.dropdown-prat-container.open').forEach(recipiente => {
                    recipiente.classList.remove('open');
                });
            }
        });

        function enviarAtualizacaoStatus(id, status) {
            fetch('../php/alternar_status_funcionario.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id_funcionario=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
            })
            .then(res => res.text())
            .then(mensagem => {
                alert(mensagem);
                if (!mensagem.startsWith('Erro')) {
                    location.reload(); 
                }
            })
            .catch(() => alert('Erro ao comunicar com o servidor.'));
        }

      });
    </script>
    
</body>
</html>