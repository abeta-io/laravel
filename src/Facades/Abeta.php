<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Abeta extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'abeta';
    }
}
