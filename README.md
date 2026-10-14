<div align="center">
    <h1>Kali Tools</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/a-mazalov/kali-tools"><img src="https://img.shields.io/packagist/v/a-mazalov/kali-tools.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/a-mazalov/kali-tools"><img src="https://img.shields.io/packagist/php-v/a-mazalov/kali-tools.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/a-mazalov/kali-tools"><img src="https://badge.laravel.cloud/badge/a-mazalov/kali-tools?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/a-mazalov/kali-tools/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/a-mazalov/kali-tools/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/a-mazalov/kali-tools"><img src="https://img.shields.io/packagist/dt/a-mazalov/kali-tools.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Utilities for a Laravel Project

## Installation

You can install the package via Composer:

```bash
composer require a-mazalov/kali-tools
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="kali-tools"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="kali-tools-config"
```

### Publishing the Translations

```bash
php artisan vendor:publish --tag="kali-tools-lang"
```

## Usage

### Production Database Guard

Once installed, the package listens for Artisan commands that change the database (`migrate`, `migrate:fresh`, `migrate:install`, `migrate:refresh`, `migrate:reset`, `migrate:rollback`, `db:seed`, `db:wipe`). When the command's connection (the `--database` option or `database.default`) points to a production database, the package prints the connection, username, database name, and host, then asks for an additional confirmation. If the confirmation is declined, or the command runs non-interactively, the command is aborted with a non-zero exit code before it touches the database.

List production hosts and database names (Oracle service names and SIDs included) in your `.env` (comma-separated, case-insensitive):

```dotenv
KALI_TOOLS_PRODUCTION_DB_HOSTS=prod-db.example.com,10.0.0.5
KALI_TOOLS_PRODUCTION_DB_DATABASES=PRODSRV,PRODDB
```

A connection is considered production when any value of the fields checked for its driver matches. Fields are keys of the `database.connections.*` entry (so `host` is what `DB_HOST` fills in `config/database.php`, `database` is `DB_DATABASE`), and `tns:KEY` reads a value from an Oracle `tns` descriptor:

| Driver | Hosts (`KALI_TOOLS_PRODUCTION_DB_HOSTS`) | Databases (`KALI_TOOLS_PRODUCTION_DB_DATABASES`) |
|---|---|---|
| `mysql`, `mariadb`, `pgsql`, `sqlsrv`, other drivers | `host`, `read.host`, `write.host` | `database` |
| `oracle` | `host`, `read.host`, `write.host`, `tns:HOST` | `service_name`, `database`, `tns:SERVICE_NAME`, `tns:SID` |

To change these fields or add a driver, publish the configuration file and edit `database_guard.fields`. Drivers that are not listed use `fields.default`.

This works with Oracle connections configured for [`yajra/laravel-oci8`](https://github.com/yajra/laravel-oci8) as well as MySQL, MariaDB, PostgreSQL, and SQL Server connections.

```text
 WARN The [migrate] command is about to run against a PRODUCTION database.

 Connection ........................................... oracle
 Username ......................................... APP_OWNER
 Database ............................................ PRODSRV
 Host ................................... prod-db.example.com

 Are you sure you want to run this command on the production database? (yes/no) [no]:
```

Other options:

```dotenv
# Disable the guard entirely.
KALI_TOOLS_PRODUCTION_DB_GUARD=false

# Let `--force` skip the confirmation, e.g. in deployment pipelines.
KALI_TOOLS_PRODUCTION_DB_ALLOW_FORCE=true
```

To change the list of guarded commands, publish the configuration file and edit `database_guard.commands`. Messages are available in English and Russian and can be customized by publishing the translations.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Kali Tools! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [a-mazalov](https://github.com/a-mazalov)
- [All Contributors](../../contributors)

## License

Kali Tools is open-sourced software licensed under the [MIT license](LICENSE.md).
