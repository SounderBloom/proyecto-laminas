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
    'db' => [
        'driver'   => 'Pdo_Pgsql',
        'hostname' => getenv('PGHOST'),
        'port'     => getenv('PGPORT'),
        'database' => getenv('PGDATABASE'),
        'username' => getenv('PGUSER'),
        'password' => getenv('PGPASSWORD'),
    ],
];
