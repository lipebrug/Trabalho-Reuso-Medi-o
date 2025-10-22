<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// (opcional) exigir login:
// if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$db_name = 'healthub';
$db_host = '127.0.0.1';
$db_port = '3306';
$db_user = 'root';
$db_password = 'PUC@1234';

try {
  $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
  $connError = "Erro ao conectar ao banco: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HealthHub EMR — Gerenciador de Relatórios</title>
  <link rel="stylesheet" href="relatorio.css" />
</head>
<body>
  <header class="navbar">
    <div class="navbar-left">
      <img src="img/logo.png" alt="Logo HealthHub" height="30">
    </div>
  </header>

  <main class="page">
    <!-- Toolbar: botão Voltar + busca -->
    <section class="toolbar">
      <a class="btn-back" href="adm.php" aria-label="Voltar para a página anterior">← Voltar</a>

      <form class="search" action="#" method="get" role="search">
        <div class="search-wrap">
          <span class="search-icon" aria-hidden="true"></span>
          <input type="search" name="q" class="search-input" placeholder="Search here" autocomplete="off" />
        </div>
      </form>
    </section>

    <?php if (!empty($connError)): ?>
      <div class="alert-error" role="alert" style="margin:16px 0;">
        <?= htmlspecialchars($connError) ?>
      </div>
    <?php endif; ?>

    <!-- Tabela -->
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th class="w-cod">Cód.</th>
            <th>Nome</th>
            <th class="w-criador">Criador</th>
            <th class="w-acoes center">Ações</th>
          </tr>
        </thead>

        <tbody id="reports-body">
          <?php 
            if (empty($connError)) {
              // inclui a listagem; usa $pdo desta página
              include 'listarRelatorios.php';
            } else {
              echo '<tr><td colspan="4" class="center">Não foi possível carregar os relatórios.</td></tr>';
            }
          ?>
        </tbody>
      </table>
    </div>
    <!-- Modal de confirmação -->
<div id="confirmModal" class="modal" style="display:none;">
  <div class="modal-content">
    <p id="modalMessage"></p>
    <div class="modal-actions">
      <button id="confirmYes" class="btn-yes">Sim</button>
      <button id="confirmNo" class="btn-no">Não</button>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
  const tbody = document.getElementById("reports-body");

  const modal = document.getElementById("confirmModal");
  const message = document.getElementById("modalMessage");
  const btnYes = document.getElementById("confirmYes");
  const btnNo = document.getElementById("confirmNo");

  // Estado
  let currentAction = null; // 'delete' | 'update'
  let currentId = null;
  let pendingUpdate = null; // { id, titulo }

  // Abrir/fechar modal
  function openModal(msg, action){
    currentAction = action;
    message.textContent = msg;
    modal.style.display = "flex";
  }
  function closeModal(){
    modal.style.display = "none";
    currentAction = null;
    currentId = null;
    pendingUpdate = null;
  }

  // Delegação na tabela
  tbody.addEventListener("click", function(e){
    const btn = e.target.closest("button.icon-btn");
    if (!btn) return;

    const tr = btn.closest("tr");
    if (!tr) return;

    const id = tr.dataset.id;

    const tdTitulo = tr.querySelector(".col-titulo");
    const tdAcoes  = tr.querySelector(".col-acoes");

    // DELETAR
    if (btn.classList.contains("delete")) {
      currentId = id;
      const tituloAtual = (tr.dataset.titulo || tdTitulo?.textContent || '').trim();
      openModal(`Tem certeza de que deseja apagar o relatório "${tituloAtual}" (Cód. ${id})?`, 'delete');
      return;
    }

    // ENTRAR EM EDIÇÃO
    if (btn.classList.contains("edit")) {
      if (tr.classList.contains("editing")) return;
      tr.classList.add("editing");

      // Guardar HTML original
      tdTitulo.dataset.original = tdTitulo.innerHTML;
      tdAcoes.dataset.original  = tdAcoes.innerHTML;

      const tituloAtual = (tdTitulo.textContent || '').trim();

      // Campo de edição
      tdTitulo.innerHTML = `<input type="text" class="edit-titulo" value="${escapeHtml(tituloAtual)}" />`;

      // Botões OK/Cancelar
      tdAcoes.innerHTML = `
        <button class="icon-btn ok" title="Confirmar"></button>
        <button class="icon-btn cancel" title="Cancelar"></button>
      `;
      return;
    }

    // CANCELAR EDIÇÃO
    if (btn.classList.contains("cancel")) {
      if (!tr.classList.contains("editing")) return;
      tdTitulo.innerHTML = tdTitulo.dataset.original || tdTitulo.innerHTML;
      tdAcoes.innerHTML  = tdAcoes.dataset.original  || tdAcoes.innerHTML;
      tr.classList.remove("editing");
      return;
    }

    // CONFIRMAR EDIÇÃO (abrir modal)
    if (btn.classList.contains("ok")) {
      const inputTitulo = tr.querySelector(".edit-titulo");
      const novoTitulo  = (inputTitulo?.value || '').trim();

      if (novoTitulo === '') {
        alert("O título não pode ficar vazio.");
        return;
      }

      pendingUpdate = { id, titulo: novoTitulo };
      openModal("Você tem certeza que deseja alterar o título do relatório?", 'update');
      return;
    }
  });

  // Confirmar ação no modal
  btnYes.addEventListener("click", function(){
    if (currentAction === 'delete' && currentId) {
      fetch("deleteRelatorio.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(currentId)
      })
      .then(r => r.text())
      .then(resp => {
        if (resp.trim() === "success") {
          location.reload();
        } else {
          alert(resp);
        }
      })
      .catch(err => alert("Erro: " + err))
      .finally(closeModal);
    }

    if (currentAction === 'update' && pendingUpdate) {
      const { id, titulo } = pendingUpdate;
      const params = new URLSearchParams();
      params.append("id", id);
      params.append("titulo", titulo);
      // Se quiser atualizar também o criador no futuro:
      // params.append("fk_id_usuario", "123");

      fetch("updateRelatorio.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params.toString()
      })
      .then(r => r.text())
      .then(resp => {
        if (resp.trim() === "success") {
          // Atualiza a linha sem reload
          const tr = tbody.querySelector(`tr[data-id="${CSS.escape(id)}"]`);
          if (tr) {
            const tdTitulo = tr.querySelector(".col-titulo");
            const tdAcoes  = tr.querySelector(".col-acoes");

            if (tdTitulo) tdTitulo.textContent = titulo;
            tr.dataset.titulo = titulo;

            if (tdAcoes) {
              tdAcoes.innerHTML = `
                <button class="icon-btn delete" type="button" title="Excluir" data-id="${escapeHtml(id)}" data-title="${escapeHtml(titulo)}"></button>
                <button class="icon-btn edit"   type="button" title="Editar"  data-id="${escapeHtml(id)}"></button>
              `;
            }
            tr.classList.remove("editing");
          }
        } else {
          alert(resp);
        }
      })
      .catch(err => alert("Erro: " + err))
      .finally(closeModal);
    }
  });

  btnNo.addEventListener("click", closeModal);
  modal.addEventListener("click", (e)=>{ if (e.target === modal) closeModal(); });

  // Util: escapar HTML
  function escapeHtml(str){
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }
});
</script>

<style>
/* Modal básico (igual ao de admsis) */
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  max-width: 420px;
  text-align: center;
}
.modal-actions {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  gap: 20px;
}
.btn-yes, .btn-no {
  padding: 8px 18px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
}
.btn-yes { background: #dc2626; color: #fff; }
.btn-no  { background: #e5e7eb; }
</style>

<style>
/* inputs inline para edição */
.edit-titulo {
  width: 100%;
  height: 34px;
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

/* Ícones dos botões (ajuste caminhos) */
.icon-btn.ok {
  background-image: url("img/icon-ok.png");
  background-size: 22px 22px;
}
.icon-btn.cancel {
  background-image: url("img/icon-cancel.png");
  background-size: 18px 18px;
}
.icon-btn.delete {
  background-image: url("img/icon-delete.png");
  background-size: 25px 20px;
}
.icon-btn.edit {
  background-image: url("img/icon-edit.png");
  background-size: 32px 20px;
}

/* hovers */
.icon-btn.ok:hover     { background-color: rgba(22,163,74,.14); box-shadow: 0 0 0 2px rgba(22,163,74,.12) inset; }
.icon-btn.cancel:hover { background-color: rgba(220,38,38,.12); box-shadow: 0 0 0 2px rgba(220,38,38,.12) inset; }
.icon-btn.edit:hover   { background-color: rgba(18,225,66,.12); box-shadow: 0 0 0 2px rgba(19,241,78,.12) inset; }
.icon-btn.delete:hover { background-color: rgba(220,38,38,.12); box-shadow: 0 0 0 2px rgba(220,38,38,.12) inset; }
</style>


  </main>
</body>
</html>
