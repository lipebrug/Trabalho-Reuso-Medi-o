<?php
include 'conn.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
        $stmt->execute([$id]);
        echo "success";
    } catch (Exception $e) {
        http_response_code(500);
        echo "Erro: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "ID inválido";
}
