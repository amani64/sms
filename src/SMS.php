<?php

namespace Amani64\SMS;

use Illuminate\Support\Facades\Facade;

class SMS extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'SMS';
    }
}