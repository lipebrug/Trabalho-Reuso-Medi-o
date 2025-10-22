<?php
// deleteRelatorio.php
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

if (!isset($_POST['id'])) {
  http_response_code(400);
  echo "ID inválido";
  exit;
}

$id = (int)$_POST['id'];

try {
  $stmt = $pdo->prepare("DELETE FROM Relatorio WHERE cod_relatorio = ?");
  $stmt->execute([$id]);

  if ($stmt->rowCount() > 0) {
    echo "success";
  } else {
    http_response_code(404);
    echo "Relatório não encontrado.";
  }
} catch (Exception $e) {
  http_response_code(500);
  echo "Erro: " . $e->getMessage();
}
