<?php

declare(strict_types=1);

namespace KaliTools\KaliTools\Tests;

use KaliTools\KaliTools\KaliToolsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            KaliToolsServiceProvider::class,
        ];
    }
}
