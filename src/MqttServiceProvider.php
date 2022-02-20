<?php
/**
 * Created by PhpStorm.
 * User: pegah
 * Date: 2/22/19
 * Time: 1:34 PM
 */

namespace Pegah\Mqtt;

use Illuminate\Support\ServiceProvider;
use Pegah\Mqtt\MqttClass\Mqtt;

class MqttServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/config/mqtt.php','mqtt');
        $this->publishes([
            __DIR__.'/config/mqtt.php' => config_path('mqtt.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('Mqtt',function (){

            return new Mqtt();
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('Mqtt');
    }
}
