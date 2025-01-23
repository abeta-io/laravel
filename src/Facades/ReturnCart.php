<?php
declare(strict_types=1);

namespace AbetaIO\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \AbetaIO\Laravel\Services\Cart\CartBuilder builder()
 * @method static bool execute()
 */
class ReturnCart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'return-cart';
    }
}
