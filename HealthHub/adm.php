<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se não estiver logado, redireciona para login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$usuarioLogado = $_SESSION['login'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HealthHub EMR — Administração</title>
  <link rel="stylesheet" href="adm.css" />
</head>
<body>
  <header class="navbar">
    <div class="navbar-left">
      <img src="img/logo.png" alt="Logo HealthHub" height="30">
    </div>

    <div class="navbar-right">
      <span class="user-info">👤 <?= htmlspecialchars($usuarioLogado) ?></span>
      <a></a>
      <a class="btn-logout" href="logout.php" aria-label="Sair da sessão">Sair</a>
    </div>
  </header>

  <main class="wrap">
    <h2 class="greeting">Bem-vindo, Administrador.</h2>

    <section class="tiles">
      <a class="tile" href="admsis.php" aria-label="Abrir Administração de Sistemas">
        <div class="tile-content">
          <h3>
            Administração<br>
            de Sistemas
          </h3>
        </div>
        <img src="img/icon-adms.png" alt="Administração de Sistemas">
      </a>

      <a class="tile" href="relatorio.php" aria-label="Abrir Gerenciador de Relatórios">
        <div class="tile-content">
          <h3>
            Gerenciador<br>
            de Relatórios
          </h3>
        </div>
        <img src="img/icon-relatorio.png" alt="Gerenciador de Relatórios">
      </a>
    </section>
  </main>
</body>
</html>
