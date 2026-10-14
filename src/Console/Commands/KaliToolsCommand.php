<?php

declare(strict_types=1);

namespace KaliTools\KaliTools\Console\Commands;

use Illuminate\Console\Command;

class KaliToolsCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'kali-tools:placeholder';

    /**
     * The command description.
     */
    protected $description = 'Placeholder Artisan command shipped by the package kali-tools.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('KaliTools placeholder command executed.');

        return self::SUCCESS;
    }
}
