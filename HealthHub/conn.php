<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$db_name = 'healthub';
$db_host = '127.0.0.1';
$db_port = '3306';
$db_user = 'root';
$db_password = 'PUC@1234';

try
{
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_password);
}catch(Exception $e){
    echo "Erro ao carregar página: ".$e->getMessage();
}

?>