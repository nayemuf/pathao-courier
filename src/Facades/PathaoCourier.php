<?php

namespace Nayemuf\PathaoCourier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Nayemuf\PathaoCourier\Apis\OrderApi order()
 * @method static \Nayemuf\PathaoCourier\Apis\AreaApi area()
 * @method static \Nayemuf\PathaoCourier\Apis\StoreApi store()
 * @method static \Nayemuf\PathaoCourier\Apis\PriceApi price()
 * 
 * @see \Nayemuf\PathaoCourier\PathaoCourier
 */
class PathaoCourier extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pathao.courier';
    }
}

