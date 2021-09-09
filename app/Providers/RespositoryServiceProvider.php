<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{
    UserContract,
    DesignContract,
};
use App\Repositories\Eloquent\{
    UserRepository,
    DesignRepository,
};

class RespositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(UserContract::class, UserRepository::class);
        $this->app->bind(DesignContract::class, DesignRepository::class);
    }
}
