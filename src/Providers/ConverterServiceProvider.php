<?php

namespace Converter\Providers;

use Illuminate\Support\ServiceProvider;

class ConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/converter.php',
            'converter'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/converter.php' => config_path('converter.php'),
        ], 'converter-config');
    }
}
