<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

use Entity\Usu;
use Entity\Med;
use Entity\Relatorio;

// 1) Listar todos os usuários
$repoUsu = $entityManager->getRepository(Usu::class);
$usuarios = $repoUsu->findAll();

echo "=== Usuarios ===\n";
foreach ($usuarios as $u) {
    printf("#%d login=%s tipo=%d\n", $u->getId(), $u->getLogin(), $u->getTipoUsuario());
}

// 2) Buscar por login, navegar relações (Usu -> Med, Relatorios)
$alice = $repoUsu->findOneBy(['login' => 'alice']);
echo "\n=== Alice ===\n";
if ($alice) {
    echo "ID: {$alice->getId()}\n";
    $med = $alice->getMed();
    if ($med) {
        echo "Médico: {$med->getNomeMedico()} | Esp: {$med->getEspecializacao()}\n";
    }
    echo "Relatórios:\n";
    foreach ($alice->getRelatorios() as $rel) {
        echo " - {$rel->getTitulo()}\n";
    }
}

// 3) DQL: listar títulos de relatórios com login do dono
$dql = 'SELECT r, u FROM ' . Relatorio::class . ' r JOIN r.usuario u ORDER BY r.id DESC';
$query = $entityManager->createQuery($dql);
$result = $query->getResult();

echo "\n=== Relatórios (com dono) ===\n";
foreach ($result as $r) {
    $u = $r->getUsuario();
    echo "[{$r->getId()}] {$r->getTitulo()} (de {$u->getLogin()})\n";
}