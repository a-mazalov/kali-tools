<?php

declare(strict_types=1);

return [

    'placeholder' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Database Guard
    |--------------------------------------------------------------------------
    |
    | Before any of the listed Artisan commands runs against a connection whose
    | host or database (database name, Oracle service name, or SID) is
    | listed below, the command asks for an additional confirmation. Values
    | are comma-separated and compared case-insensitively.
    |
    */

    'database_guard' => [

        'enabled' => (bool) env('KALI_TOOLS_PRODUCTION_DB_GUARD', true),

        'hosts' => array_filter(array_map('trim', explode(',', (string) env('KALI_TOOLS_PRODUCTION_DB_HOSTS', '')))),

        'databases' => array_filter(array_map('trim', explode(',', (string) env('KALI_TOOLS_PRODUCTION_DB_DATABASES', '')))),

        /*
        | Connection fields checked for each driver (the `driver` key of a
        | `database.connections.*` entry). Fields are dot-notation keys of the
        | connection config, e.g. `host` is filled from DB_HOST and `database`
        | from DB_DATABASE in config/database.php. Use `tns:KEY` to read a value
        | from an Oracle TNS descriptor, e.g. `tns:HOST`. Drivers that are not
        | listed fall back to `default`.
        */
        'fields' => [

            'default' => [
                'hosts' => ['host', 'read.host', 'write.host'],
                'databases' => ['database'],
            ],

            'mysql' => [
                'hosts' => ['host', 'read.host', 'write.host'],
                'databases' => ['database'],
            ],

            'mariadb' => [
                'hosts' => ['host', 'read.host', 'write.host'],
                'databases' => ['database'],
            ],

            'pgsql' => [
                'hosts' => ['host', 'read.host', 'write.host'],
                'databases' => ['database'],
            ],

            'sqlsrv' => [
                'hosts' => ['host', 'read.host', 'write.host'],
                'databases' => ['database'],
            ],

            'oracle' => [
                'hosts' => ['host', 'read.host', 'write.host', 'tns:HOST'],
                'databases' => ['service_name', 'database', 'tns:SERVICE_NAME', 'tns:SID'],
            ],

        ],

        // When true, passing --force skips the confirmation (e.g. in deploy pipelines).
        'allow_force' => (bool) env('KALI_TOOLS_PRODUCTION_DB_ALLOW_FORCE', false),

        'commands' => [
            'migrate',
            'migrate:fresh',
            'migrate:install',
            'migrate:refresh',
            'migrate:reset',
            'migrate:rollback',
            'db:seed',
            'db:wipe',
        ],

    ],

];
