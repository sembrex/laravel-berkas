<?php

namespace Karogis\Berkas;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Karogis\Berkas\Berkas
 */
class BerkasFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'berkas';
    }
}
