---
name: kali-tools-development
description: >
  Configure and apply the Kali Tools package in Laravel applications.
license: MIT
metadata:
  author: a-mazalov
---

# Kali Tools

Use this skill when a Laravel application needs to integrate the Kali Tools package.

## Primary Goal

- apply the `a-mazalov/kali-tools` package's public API in the smallest correct way

## Workflow

### 1. Inspect the Laravel app context

- confirm the app is a Laravel project
- inspect the target code paths where the package should be applied

### 2. Configure the production database guard

The guard is enabled automatically after `composer require a-mazalov/kali-tools`. It asks for an extra confirmation before `migrate`, `migrate:fresh`, `migrate:install`, `migrate:refresh`, `migrate:reset`, `migrate:rollback`, `db:seed`, and `db:wipe` run against a production connection, showing the connection, username, database, and host.

- add production hosts and database names to `.env` (comma-separated, case-insensitive):
  - `KALI_TOOLS_PRODUCTION_DB_HOSTS` matches `host`, `read.host`, `write.host`, and Oracle `tns` `HOST` entries
  - `KALI_TOOLS_PRODUCTION_DB_DATABASES` matches Oracle `service_name`, `database`, and Oracle `tns` `SERVICE_NAME`/`SID` entries
  - the checked fields are set per driver in `database_guard.fields` (`mysql`, `mariadb`, `pgsql`, `sqlsrv`, `oracle`, fallback `default`); entries are connection config keys or `tns:KEY`
- set `KALI_TOOLS_PRODUCTION_DB_ALLOW_FORCE=true` only if deployment pipelines must run `migrate --force` against production without a prompt
- set `KALI_TOOLS_PRODUCTION_DB_GUARD=false` to disable the guard
- publish config with `php artisan vendor:publish --tag="kali-tools-config"` to change `database_guard.commands`

Declined or non-interactive runs throw `KaliTools\KaliTools\Exceptions\ProductionDatabaseCommandAborted` and exit with a non-zero code before the database is touched.

## Rules, References, and Templates

Read before executing:

- no additional resource files for this skill

## Examples

- Oracle production behind a TNS descriptor: set `KALI_TOOLS_PRODUCTION_DB_DATABASES=PRODSRV`; `php artisan migrate --database=oracle` now prompts before running.
- CI deploy that runs `php artisan migrate --force` on production: set `KALI_TOOLS_PRODUCTION_DB_ALLOW_FORCE=true` in the production environment.

## Anti-patterns

- do not document package internals here; keep the skill focused on adoption in Laravel apps
- do not call `env()` for the guard lists in app code; use the `kali-tools.database_guard.*` config keys
