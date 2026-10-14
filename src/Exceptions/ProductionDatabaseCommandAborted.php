<?php

declare(strict_types=1);

namespace KaliTools\KaliTools\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use RuntimeException;

class ProductionDatabaseCommandAborted extends RuntimeException implements ShouldntReport
{
    //
}
