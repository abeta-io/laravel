<?php

namespace AbetaIO\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Abeta extends Facade
{
    /**
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'abeta';
    }
}