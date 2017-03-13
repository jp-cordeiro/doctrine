<?php

/**Configurações de anotações**/
use Doctrine\ORM\Tools\Setup;

/**Gerencia todas as entidades da aplicacao**/
use Doctrine\ORM\EntityManager;

/**Caminho das entidades**/
$paths = [
    __DIR__.'/Entity'
];
$isDevMode = true;

//Configurações do BD
$dbParams = [
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'dbname' => 'doctrine_estudo'
];

/**Configuração do ambiente doctrine por mapeamento via annotations**/
$config = Setup::createAnnotationMetadataConfiguration($paths,$isDevMode);

/**Configuração do gerenciador de entidades passando as configurações do bd e do doctrine como paramentro**/
$entityManager = EntityManager::create($dbParams,$config);

function getEntityManager(){
    global $entityManager;
    return $entityManager;
}