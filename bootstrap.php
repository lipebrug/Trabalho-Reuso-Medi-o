<?php
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once "vendor/autoload.php";

// Diretório das entidades
$paths = [__DIR__ . '/src/Entity'];

// Ativa modo de desenvolvimento
$isDevMode = true;

// Define o diretório de proxies
$proxyDir = __DIR__ . '/proxies';
if (!is_dir($proxyDir)) {
    mkdir($proxyDir, 0777, true);
}

// Cria configuração
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: $paths,
    isDevMode: $isDevMode,
    proxyDir: $proxyDir
);

// Configuração do banco (SQLite local)
$connection = DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
], $config);

// Cria o EntityManager
$entityManager = new EntityManager($connection, $config);
