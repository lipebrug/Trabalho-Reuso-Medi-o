<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php'; // precisa expor $entityManager

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

// Carrega metadados das entidades automaticamente do diretório /src
$cmf = $entityManager->getMetadataFactory();      // ClassMetadataFactory
if (!$cmf instanceof ClassMetadataFactory) {
    throw new RuntimeException('MetadataFactory inválido');
}
$allMetadata = $cmf->getAllMetadata();

if (empty($allMetadata)) {
    echo "Nenhuma entidade encontrada. Verifique namespace/paths em bootstrap.php\n";
    exit(1);
}

$tool = new SchemaTool($entityManager);

// DICA: em dev é comum dropar e recriar; em prod, prefira migrações.
$tool->dropSchema($allMetadata);
$tool->createSchema($allMetadata);

echo "Schema criado/atualizado com sucesso!\n";
