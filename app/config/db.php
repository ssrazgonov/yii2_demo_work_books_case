<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . getenv('DB_HOST', 'localhost') . ';dbname=' . getenv('DB_NAME', 'local'),
    'username' => getenv('DB_USER', 'user'),
    'password' => getenv('DB_PASS', 'password'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
