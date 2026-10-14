<?php

declare(strict_types=1);

namespace KaliTools\KaliTools;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use KaliTools\KaliTools\Console\Commands\KaliToolsCommand;
use KaliTools\KaliTools\Listeners\ConfirmProductionDatabaseCommand;

class KaliToolsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kali-tools.php', 'kali-tools');

        $this->app->singleton(KaliTools::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/kali-tools.php');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'kali-tools');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/kali-tools.php' => config_path('kali-tools.php'),
        ], ['kali-tools', 'kali-tools-config']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/kali-tools'),
        ], ['kali-tools', 'kali-tools-lang']);

        $this->commands([
            KaliToolsCommand::class,
        ]);

        Event::listen(CommandStarting::class, ConfirmProductionDatabaseCommand::class);
    }
}
