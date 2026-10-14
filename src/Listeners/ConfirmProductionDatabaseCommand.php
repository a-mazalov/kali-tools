<?php

declare(strict_types=1);

namespace KaliTools\KaliTools\Listeners;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use KaliTools\KaliTools\Exceptions\ProductionDatabaseCommandAborted;

class ConfirmProductionDatabaseCommand
{
    public function __construct(
        protected Application $app,
        protected Repository $config,
    ) {}

    /**
     * Ask for confirmation before a guarded command touches a production database.
     *
     * @throws ProductionDatabaseCommandAborted
     */
    public function handle(CommandStarting $event): void
    {
        if (! $this->config->get('kali-tools.production_database_guard.enabled', true)) {
            return;
        }

        if (! in_array($event->command, (array) $this->config->get('kali-tools.production_database_guard.commands', []), true)) {
            return;
        }

        $connection = $this->connectionName($event);

        /** @var array<string, mixed> $settings */
        $settings = (array) $this->config->get("database.connections.{$connection}", []);

        $hosts = $this->hosts($settings);
        $services = $this->services($settings);

        if (! $this->isProduction($hosts, $services)) {
            return;
        }

        $output = $this->app->make(OutputStyle::class, ['input' => $event->input, 'output' => $event->output]);
        $components = $this->app->make(Factory::class, ['output' => $output]);

        $components->warn(trans('kali-tools::messages.production_database_guard.warning', ['command' => $event->command]));

        $components->twoColumnDetail(trans('kali-tools::messages.production_database_guard.connection'), $connection);
        $components->twoColumnDetail(trans('kali-tools::messages.production_database_guard.username'), $this->stringValue($settings['username'] ?? null));
        $components->twoColumnDetail(trans('kali-tools::messages.production_database_guard.database'), implode(', ', $services) ?: '-');
        $components->twoColumnDetail(trans('kali-tools::messages.production_database_guard.host'), implode(', ', $hosts) ?: '-');
        $output->newLine();

        if ($this->config->get('kali-tools.production_database_guard.allow_force', false)
            && $event->input->hasOption('force')
            && $event->input->getOption('force')) {
            return;
        }

        if ($event->input->isInteractive()
            && $output->confirm(trans('kali-tools::messages.production_database_guard.question'), false)) {
            return;
        }

        throw new ProductionDatabaseCommandAborted(
            trans('kali-tools::messages.production_database_guard.aborted', ['command' => $event->command]),
        );
    }

    /**
     * Resolve the connection the command will use.
     */
    protected function connectionName(CommandStarting $event): string
    {
        $database = $event->input->hasOption('database') ? $event->input->getOption('database') : null;

        return is_string($database) && $database !== ''
            ? $database
            : $this->stringValue($this->config->get('database.default'));
    }

    /**
     * Determine if any host or service matches the configured production list.
     *
     * @param  list<string>  $hosts
     * @param  list<string>  $services
     */
    protected function isProduction(array $hosts, array $services): bool
    {
        $normalize = fn (mixed $values): array => array_map(
            fn (mixed $value): string => mb_strtolower($this->stringValue($value)),
            (array) $values,
        );

        return array_intersect($normalize($hosts), $normalize($this->config->get('kali-tools.production_database_guard.hosts', []))) !== []
            || array_intersect($normalize($services), $normalize($this->config->get('kali-tools.production_database_guard.services', []))) !== [];
    }

    /**
     * Collect hosts from the connection, including read/write replicas and Oracle TNS descriptors.
     *
     * @param  array<string, mixed>  $settings
     * @return list<string>
     */
    protected function hosts(array $settings): array
    {
        $hosts = [
            ...(array) ($settings['host'] ?? []),
            ...(array) Arr::get($settings, 'read.host', []),
            ...(array) Arr::get($settings, 'write.host', []),
            ...$this->tnsValues($settings, 'HOST'),
        ];

        return $this->unique($hosts);
    }

    /**
     * Collect service names, SIDs, and database names from the connection.
     *
     * @param  array<string, mixed>  $settings
     * @return list<string>
     */
    protected function services(array $settings): array
    {
        return $this->unique([
            $settings['service_name'] ?? null,
            $settings['database'] ?? null,
            ...$this->tnsValues($settings, 'SERVICE_NAME'),
            ...$this->tnsValues($settings, 'SID'),
        ]);
    }

    /**
     * Extract values for a key from an Oracle TNS descriptor, e.g. (HOST = db.example.com).
     *
     * @param  array<string, mixed>  $settings
     * @return list<string>
     */
    protected function tnsValues(array $settings, string $key): array
    {
        $tns = $this->stringValue($settings['tns'] ?? null);

        preg_match_all('/\(\s*'.$key.'\s*=\s*([^)\s]+)\s*\)/i', $tns, $matches);

        return $matches[1];
    }

    /**
     * @param  array<int, mixed>  $values
     * @return list<string>
     */
    protected function unique(array $values): array
    {
        return array_values(array_unique(array_filter(
            array_map($this->stringValue(...), $values),
            fn (string $value): bool => $value !== '',
        )));
    }

    protected function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
