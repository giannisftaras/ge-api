<?php

namespace GE;
use Illuminate\Support\ServiceProvider;

class GEServiceProvider extends ServiceProvider {
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('GE', GE\GE::class);
    }
}


?>