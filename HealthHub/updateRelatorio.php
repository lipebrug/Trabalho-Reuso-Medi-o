<?php
// updateRelatorio.php
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

$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$titulo = trim($_POST['titulo'] ?? '');
$criadorId = isset($_POST['fk_id_usuario']) && $_POST['fk_id_usuario'] !== '' ? (int)$_POST['fk_id_usuario'] : null;

if ($id <= 0 || $titulo === '') {
  http_response_code(400);
  echo "Parâmetros inválidos.";
  exit;
}

try {
  if ($criadorId !== null) {
    // Atualiza título e (opcionalmente) o criador, se enviado
    $st = $pdo->prepare("UPDATE Relatorio SET titulo_relatorio = ?, fk_id_usuario = ? WHERE cod_relatorio = ?");
    $st->execute([$titulo, $criadorId, $id]);
  } else {
    // Atualiza só o título
    $st = $pdo->prepare("UPDATE Relatorio SET titulo_relatorio = ? WHERE cod_relatorio = ?");
    $st->execute([$titulo, $id]);
  }

  echo "success";
} catch (Exception $e) {
  http_response_code(500);
  echo "Erro ao atualizar: " . $e->getMessage();
}
