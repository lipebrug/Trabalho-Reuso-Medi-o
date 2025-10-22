<?php 
session_start();

$db_name = 'healthub';
$db_host = '127.0.0.1';
$db_port = '3306';
$db_user = 'root';
$db_password = 'PUC@1234';

try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
    $pdo = null;
    $connError = "Erro ao carregar página: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HealthHub EMR — Administração de Sistemas</title>
  <link rel="stylesheet" href="admsis.css" />
</head>
<body>
  <header class="navbar">
    <div class="navbar-left">
      <img src="img/logo.png" alt="Logo HealthHub" height="30">
    </div>
  </header>

  <main class="page">
  <section class="toolbar">
    <a class="btn-back" href="adm.php" aria-label="Voltar para a página anterior">← Voltar</a>

    <form class="search" action="#" method="get" role="search">
      <div class="search-wrap">
        <span class="search-icon" aria-hidden="true"></span>
        <input type="search" name="q" class="search-input" placeholder="Search here" autocomplete="off"/>
      </div>
    </form>
  </section>

  <?php if (isset($connError)): ?>
    <div class="alert-error" role="alert" style="margin:16px 0;">
      <?= htmlspecialchars($connError) ?>
    </div>
  <?php endif; ?>

    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:120px;">ID</th>
            <th>Usuário</th>
            <th>Nome</th>
            <th style="width:220px;">Tipo de Perfil</th>
            <th style="width:220px;">Senha</th>
            <th style="width:140px;" class="center">Ações</th>
          </tr>
        </thead>

        <tbody id="users-body">
          <?php 
            if (!isset($connError)) {
              include 'listarUsuarios.php';
            } else {
              echo '<tr><td colspan="6" class="center">Não foi possível carregar os usuários.</td></tr>';
            }
          ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal de confirmação (reutilizado para deletar ou atualizar) -->
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
  const tbody = document.getElementById("users-body");

  const modal = document.getElementById("confirmModal");
  const message = document.getElementById("modalMessage");
  const btnYes = document.getElementById("confirmYes");
  const btnNo = document.getElementById("confirmNo");

  // Estado da ação atual
  let currentAction = null; // 'delete' | 'update'
  let currentId = null;     // id_usuario do alvo
  let pendingUpdate = null; // { id, tipo, nome, senha }

  // Helpers
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

  // Delegação de eventos na tabela
  tbody.addEventListener("click", function(e){
    const btn = e.target.closest("button.icon-btn, a.icon-link");
    if (!btn) return;
    const tr = btn.closest("tr");
    if (!tr) return;

    // Pega infos da linha
    const id = tr.dataset.id;
    const login = tr.dataset.login;
    const tipo = tr.dataset.tipo; // '1' ou '2'

    // Achar células por classe
    const tdNome  = tr.querySelector(".col-nome");
    const tdSenha = tr.querySelector(".col-senha");
    const tdAcoes = tr.querySelector(".col-acoes");

    // CLICK: deletar
    if (btn.classList.contains("delete")) {
      currentId = id;
      openModal(`Tem certeza de que deseja apagar o usuário (${login})?`, 'delete');
      return;
    }

    // CLICK: entrar em edição
    if (btn.classList.contains("edit")) {
      if (tr.classList.contains("editing")) return; // já está editando
      tr.classList.add("editing");

      // Guarda HTML original para cancelar
      tdNome.dataset.original = tdNome.innerHTML;
      tdSenha.dataset.original = tdSenha.innerHTML;
      tdAcoes.dataset.original = tdAcoes.innerHTML;

      const nomeAtual = tdNome.textContent.trim();

      // Inputs de edição
      tdNome.innerHTML = `<input type="text" class="edit-nome" value="${escapeHtml(nomeAtual)}" />`;
      tdSenha.innerHTML = `<input type="password" class="edit-senha" placeholder="Digite nova senha (opcional)" />`;

      // Troca botões de ação para OK/Cancelar
      tdAcoes.innerHTML = `
        <button class="icon-btn ok" title="Confirmar"></button>
        <button class="icon-btn cancel" title="Cancelar"></button>
      `;

      return;
    }

    // CLICK: cancelar edição
    if (btn.classList.contains("cancel")) {
      if (!tr.classList.contains("editing")) return;
      tdNome.innerHTML  = tdNome.dataset.original || tdNome.innerHTML;
      tdSenha.innerHTML = tdSenha.dataset.original || tdSenha.innerHTML;
      tdAcoes.innerHTML = tdAcoes.dataset.original || tdAcoes.innerHTML;
      tr.classList.remove("editing");
      return;
    }

    // CLICK: confirmar edição (abrir modal)
    if (btn.classList.contains("ok")) {
      const inputNome  = tr.querySelector(".edit-nome");
      const inputSenha = tr.querySelector(".edit-senha");

      const novoNome  = (inputNome?.value || '').trim();
      const novaSenha = (inputSenha?.value || '').trim(); // opcional

      if (novoNome === '') {
        alert("O campo Nome não pode ficar vazio.");
        return;
      }

      pendingUpdate = { id, tipo, nome: novoNome, senha: novaSenha };
      openModal("Você tem certeza que deseja alterar as informações do perfil?", 'update');
      return;
    }
  });

  // Confirmar no modal
  btnYes.addEventListener("click", function(){
    if (currentAction === 'delete' && currentId) {
      fetch("deleteUsuario.php", {
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
      const { id, tipo, nome, senha } = pendingUpdate;
      const params = new URLSearchParams();
      params.append("id", id);
      params.append("tipo", tipo);
      params.append("nome", nome);
      params.append("senha", senha); // pode vir vazio

      fetch("updateUsuario.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params.toString()
      })
      .then(r => r.text())
      .then(resp => {
        if (resp.trim() === "success") {
          // Atualiza a linha sem recarregar:
          const tr = tbody.querySelector(`tr[data-id="${CSS.escape(id)}"]`);
          if (tr) {
            const tdNome  = tr.querySelector(".col-nome");
            const tdSenha = tr.querySelector(".col-senha");
            const tdAcoes = tr.querySelector(".col-acoes");

            if (tdNome)  tdNome.textContent  = nome;
            if (tdSenha) tdSenha.textContent = '••••••••'; // mantém máscara

            if (tdAcoes) {
              tdAcoes.innerHTML = `
                <button class="icon-btn delete" title="Excluir"></button>
                <button class="icon-btn edit"   title="Editar"></button>
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

  // Utilitário: escapar HTML para inputs
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
/* Modal básico */
.modal {
  position: fixed; inset: 0;
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
  display: flex; justify-content: center; gap: 20px;
}
.btn-yes, .btn-no {
  padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;
}
.btn-yes { background: #16a34a; color: #fff; }   /* verde confirmar */
.btn-no  { background: #e5e7eb; }

/* Ícones dos botões (ajuste os caminhos das imagens) */
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

/* realce nos hovers */
.icon-btn.ok:hover     { background-color: rgba(22,163,74,.14); box-shadow: 0 0 0 2px rgba(22,163,74,.12) inset; }
.icon-btn.cancel:hover { background-color: rgba(220,38,38,.12); box-shadow: 0 0 0 2px rgba(220,38,38,.12) inset; }
.icon-btn.edit:hover   { background-color: rgba(18,225,66,.12); box-shadow: 0 0 0 2px rgba(19,241,78,.12) inset; }
.icon-btn.delete:hover { background-color: rgba(220,38,38,.12); box-shadow: 0 0 0 2px rgba(220,38,38,.12) inset; }

/* inputs inline */
.edit-nome, .edit-senha {
  width: 100%;
  height: 34px;
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}
</style>

</body>
</html>
