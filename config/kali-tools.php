<?php

declare(strict_types=1);

return [

    'placeholder' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Production Database Guard
    |--------------------------------------------------------------------------
    |
    | Before any of the listed Artisan commands runs against a connection whose
    | host or service name (Oracle service name, SID, or database name) is
    | listed below, the command asks for an additional confirmation. Values
    | are comma-separated and compared case-insensitively.
    |
    */

    'production_database_guard' => [

        'enabled' => (bool) env('KALI_TOOLS_PRODUCTION_DB_GUARD', true),

        'hosts' => array_filter(array_map('trim', explode(',', (string) env('KALI_TOOLS_PRODUCTION_DB_HOSTS', '')))),

        'services' => array_filter(array_map('trim', explode(',', (string) env('KALI_TOOLS_PRODUCTION_DB_SERVICES', '')))),

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
