<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

use Entity\Usu;
use Entity\Med;
use Entity\Adm;
use Entity\Relatorio;

// senha “hash” só pra exemplo; em app real use password_hash() adequadamente.
$u1 = new Usu(login: 'alice', senhaHash: password_hash('123', PASSWORD_BCRYPT), tipoUsuario: 1); // 1=medico
$u2 = new Usu(login: 'bob',   senhaHash: password_hash('123', PASSWORD_BCRYPT), tipoUsuario: 2); // 2=adm

$med = new Med(usuario: $u1, nome: 'Dra. Alice Pereira', esp: 'Cardiologia');
$adm = new Adm(usuario: $u2, nomeAdm: 'Bob Admin');

$r1 = new Relatorio(usuario: $u1, titulo: 'Relatório inicial da Dra. Alice');
$r2 = new Relatorio(usuario: $u1, titulo: 'Acompanhamento 2');
$r3 = new Relatorio(usuario: $u2, titulo: 'Checklist administrativo');

$entityManager->persist($u1);
$entityManager->persist($u2);
$entityManager->persist($med);
$entityManager->persist($adm);
$entityManager->persist($r1);
$entityManager->persist($r2);
$entityManager->persist($r3);

$entityManager->flush();

echo "Dados inseridos!\n";
