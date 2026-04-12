<?php

namespace MarcelWeidum\BackButton\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MarcelWeidum\BackButton\BackButton
 */
class BackButton extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MarcelWeidum\BackButton\BackButton::class;
    }
}
