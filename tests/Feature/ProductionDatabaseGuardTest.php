<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use KaliTools\KaliTools\Exceptions\ProductionDatabaseCommandAborted;

beforeEach(function () {
    // Laravel only forwards console events outside of unit tests, so enable it explicitly.
    $this->app->make(Kernel::class)->rerouteSymfonyCommandEvents();

    config([
        'kali-tools.production_database_guard.hosts' => ['PROD-DB.example.com'],
        'kali-tools.production_database_guard.services' => ['PRODSRV'],
        'database.connections.oracle_service' => [
            'driver' => 'oracle',
            'host' => 'prod-db.example.com',
            'port' => '1521',
            'service_name' => 'ORCL',
            'username' => 'APP_OWNER',
            'password' => 'secret',
        ],
        'database.connections.oracle_tns' => [
            'driver' => 'oracle',
            'tns' => '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.0.5)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=prodsrv)))',
            'username' => 'TNS_USER',
            'password' => 'secret',
        ],
        'database.connections.oracle_dev' => [
            'driver' => 'oracle',
            'host' => 'dev-db.example.com',
            'service_name' => 'DEVSRV',
            'username' => 'DEV_USER',
            'password' => 'secret',
        ],
    ]);
});

it('registers the guard config defaults', function () {
    expect(config('kali-tools.production_database_guard.enabled'))->toBeTrue()
        ->and(config('kali-tools.production_database_guard.allow_force'))->toBeFalse()
        ->and(config('kali-tools.production_database_guard.commands'))->toContain('migrate', 'migrate:rollback', 'migrate:fresh', 'db:wipe');
});

it('shows the connection details and aborts when an oracle host is production and the user declines', function () {
    expect(fn () => $this->artisan('migrate', ['--database' => 'oracle_service'])
        ->expectsOutputToContain('PRODUCTION database')
        ->expectsOutputToContain('APP_OWNER')
        ->expectsOutputToContain('ORCL')
        ->expectsOutputToContain('prod-db.example.com')
        ->expectsConfirmation('Are you sure you want to run this command on the production database?', 'no')
        ->run(),
    )->toThrow(ProductionDatabaseCommandAborted::class, 'The [migrate] command was cancelled');
});

it('detects a production oracle service name inside a tns descriptor', function () {
    expect(fn () => $this->artisan('migrate:rollback', ['--database' => 'oracle_tns'])
        ->expectsOutputToContain('TNS_USER')
        ->expectsOutputToContain('prodsrv')
        ->expectsOutputToContain('10.0.0.5')
        ->expectsConfirmation('Are you sure you want to run this command on the production database?', 'no')
        ->run(),
    )->toThrow(ProductionDatabaseCommandAborted::class);
});

it('uses the default connection when no database option is given', function () {
    config(['database.default' => 'oracle_service']);

    expect(fn () => $this->artisan('db:wipe')
        ->expectsConfirmation('Are you sure you want to run this command on the production database?', 'no')
        ->run(),
    )->toThrow(ProductionDatabaseCommandAborted::class, 'The [db:wipe] command was cancelled');
});

it('runs the command when the user confirms', function () {
    config(['database.connections.testing.host' => 'prod-db.example.com']);

    $this->artisan('migrate', ['--database' => 'testing'])
        ->expectsConfirmation('Are you sure you want to run this command on the production database?', 'yes')
        ->assertSuccessful();
});

it('does not ask for non production connections', function () {
    $this->artisan('migrate', ['--database' => 'testing'])
        ->doesntExpectOutputToContain('PRODUCTION database')
        ->assertSuccessful();
});

it('aborts in non interactive mode', function () {
    expect(fn () => $this->artisan('migrate', ['--database' => 'oracle_service', '--no-interaction' => true])->run())
        ->toThrow(ProductionDatabaseCommandAborted::class);
});

it('aborts with --force unless forcing is allowed', function () {
    expect(fn () => $this->artisan('migrate', ['--database' => 'oracle_service', '--force' => true, '--no-interaction' => true])->run())
        ->toThrow(ProductionDatabaseCommandAborted::class);
});

it('skips the confirmation with --force when allowed', function () {
    config([
        'kali-tools.production_database_guard.allow_force' => true,
        'database.connections.testing.host' => 'prod-db.example.com',
    ]);

    $this->artisan('migrate', ['--database' => 'testing', '--force' => true, '--no-interaction' => true])
        ->expectsOutputToContain('PRODUCTION database')
        ->assertSuccessful();
});

it('ignores commands that are not guarded', function () {
    config(['database.default' => 'oracle_service']);

    $this->artisan('kali-tools:placeholder')
        ->doesntExpectOutputToContain('PRODUCTION database')
        ->assertSuccessful();
});

it('can be disabled', function () {
    config([
        'kali-tools.production_database_guard.enabled' => false,
        'database.connections.testing.host' => 'prod-db.example.com',
    ]);

    $this->artisan('migrate', ['--database' => 'testing'])
        ->doesntExpectOutputToContain('PRODUCTION database')
        ->assertSuccessful();
});

it('ships russian translations', function () {
    app()->setLocale('ru');

    expect(trans('kali-tools::messages.production_database_guard.username'))->toBe('Пользователь');
});
