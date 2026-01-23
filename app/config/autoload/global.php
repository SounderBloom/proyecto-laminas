<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return [
    /*'db' => [
        'driver'         => 'Pdo',
        'dsn'            => 'pgsql:host=laminas_db;port=5432;dbname=laminas',
        'username'       => 'laminas',
        'password'       => 'secret',
        'driver_options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    ],*/
    'db' => [
        'driver'   => 'Pdo',
        'dsn'      => sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            getenv('PGHOST'),
            getenv('PGPORT'),
            getenv('PGDATABASE')
        ),
        'username' => getenv('PGUSER'),
        'password' => getenv('PGPASSWORD'),
    ],
];
