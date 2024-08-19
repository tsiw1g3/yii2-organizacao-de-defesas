<?php

return [
    'class' => 'yii\db\Connection',
    // =================================== Remote MySQL Credentials ===================================
    // 'dsn' => 'mysql:host=dokku-mysql-sistema-de-defesas-api;dbname=sistema_de_defesas_api;port=3306',    
    // 'username' => 'mysql',
    // 'password' => '6e879fa479780d73',
    // ================================================================================================
    // =================================== Local MySQL Credentials ====================================
    'dsn' => 'mysql:host=host.docker.internal:3309;dbname=organizacao_defesa',
    'username' => 'root',
    'password' => 'root',
    // ================================================================================================
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
