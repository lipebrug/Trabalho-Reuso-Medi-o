<?php
// updateUsuario.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$db_name = 'healthub';
$db_host = '127.0.0.1';
$db_port = '3306';
$db_user = 'root';
$db_password = 'PUC@1234';

try {
  $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
  http_response_code(500);
  echo "Erro de conexão: " . $e->getMessage();
  exit;
}

$id   = isset($_POST['id'])   ? (int)$_POST['id'] : 0;
$tipo = isset($_POST['tipo']) ? (int)$_POST['tipo'] : 0;
$nome = trim($_POST['nome'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if ($id <= 0 || $nome === '' || ($tipo !== 1 && $tipo !== 2)) {
  http_response_code(400);
  echo "Parâmetros inválidos.";
  exit;
}

try {
  $pdo->beginTransaction();

  // Atualiza nome conforme tipo
  if ($tipo === 1) {
    $st = $pdo->prepare("UPDATE Administrador SET nome_adm = ? WHERE fk_id_usuario = ?");
    $st->execute([$nome, $id]);
  } else if ($tipo === 2) {
    $st = $pdo->prepare("UPDATE Medico SET nome_medico = ? WHERE fk_id_usuario = ?");
    $st->execute([$nome, $id]);
  }

  // Atualiza senha somente se enviada
  if ($senha !== '') {
    // Para usar hash depois:
    // $hash = password_hash($senha, PASSWORD_DEFAULT);
    // $st = $pdo->prepare("UPDATE Usuario SET senha = ? WHERE id_usuario = ?");
    // $st->execute([$hash, $id]);

    $st = $pdo->prepare("UPDATE Usuario SET senha = ? WHERE id_usuario = ?");
    $st->execute([$senha, $id]);
  }

  $pdo->commit();
  echo "success";
} catch (Exception $e) {
  $pdo->rollBack();
  http_response_code(500);
  echo "Erro ao atualizar: " . $e->getMessage();
}
