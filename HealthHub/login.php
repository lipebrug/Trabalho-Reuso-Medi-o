<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/* === Conexão === */
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

/* === Processa login (POST) === */
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($connError)) {
  $usuario = trim($_POST['usuario'] ?? '');
  $senha   = $_POST['senha'] ?? '';

  if ($usuario === '' || $senha === '') {
    $erro = 'Informe usuário e senha.';
  } else {
    // Busca pelo login
    $stmt = $pdo->prepare("SELECT id_usuario, login, senha, tipo_usuario FROM Usuario WHERE login = ? LIMIT 1");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      $erro = 'Usuário ou senha inválidos.';
    } else {
      $senhaDB = $user['senha'];

      // Se você usa hash (recomendado), troque a linha de validação pela password_verify
      // Exemplo:
      // if (password_verify($senha, $senhaDB)) { ... }
      // Como hoje a senha está em texto puro, comparamos diretamente:
      if ($senha === $senhaDB /* substitua por password_verify($senha, $senhaDB) se usar hash */) {
        // Login OK → cria sessão
        $_SESSION['id_usuario']  = (int)$user['id_usuario'];
        $_SESSION['login']       = $user['login'];
        $_SESSION['tipo_usuario']= (int)$user['tipo_usuario'];

        // Redireciona conforme o tipo
        if ((int)$user['tipo_usuario'] === 1) {
          header('Location: adm.html');
          exit;
        } elseif ((int)$user['tipo_usuario'] === 2) {
          header('Location: medico.html');
          exit;
        } else {
          // Tipo desconhecido → trate como quiser
          $erro = 'Tipo de usuário não reconhecido.';
        }
      } else {
        $erro = 'Usuário ou senha inválidos.';
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HealthHub EMR - Login</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <header class="navbar">
    <div class="navbar-left">
      <img src="img/logo.png" alt="Logo HealthHub" height="30">
    </div>
    <div class="navbar-right">
      <a class="btn-logout" href="index.html" aria-label="Sair da sessão">Sair</a>
    </div>
  </header>

  <main class="login-container">
    <h1>Login</h1>
    <p>Use sua conta para entrar.</p>

    <?php if (!empty($connError)): ?>
      <div class="alert-error" role="alert" style="margin-bottom:12px;">
        <?= htmlspecialchars($connError) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($erro)): ?>
      <div class="alert-error" role="alert" style="margin-bottom:12px;">
        <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>

    <!-- Formulário -->
    <form class="login-form" id="loginForm" method="post" action="login.php" novalidate>
      <label for="usuario">Usuário</label>
      <input type="text" id="usuario" name="usuario" required value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">

      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" required>

      <button type="submit">Entrar</button>
    </form>
  </main>

  <!-- Removemos o redirecionamento JS; agora o PHP valida e redireciona -->
</body>
</html>
