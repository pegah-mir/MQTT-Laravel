<?php
/**
 * Created by PhpStorm.
 * User: pegah
 * Date: 2/27/19
 * Time: 12:03 PM
 */

namespace Pegah\Mqtt\Facades;

use Illuminate\Support\Facades\Facade;

class Mqtt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Mqtt';
    }

}
