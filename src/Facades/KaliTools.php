<?php

declare(strict_types=1);

namespace KaliTools\KaliTools\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KaliTools\KaliTools\KaliTools
 */
class KaliTools extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \KaliTools\KaliTools\KaliTools::class;
    }
}
