<?php

namespace App\Providers;

use App\Kernel\Logics\Providers\MessagesLogic;
use App\Kernel\Logics\Providers\ArrayLogic;
use App\Kernel\Logics\Providers\ResponseLogic;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('messages', function () {
            return new MessagesLogic();
        });
        $this->app->singleton('arrays', function () {
            return new ArrayLogic();
        });
        $this->app->singleton('responseCustom', function () {
            return new ResponseLogic();
        });
    }

    public function boot()
    {
        $responseCustom = app('responseCustom');
        Response::macro('create', [$responseCustom, 'responseCreate']);
        Response::macro('data', [$responseCustom, 'responseData']);
        Response::macro('paging', [$responseCustom, 'responsePaging']);
        Response::macro('unauthenticated', [$responseCustom, 'responseUnauthenticated']);
        Response::macro('unauthorized', [$responseCustom, 'responseUnauthorized']);
        Response::macro('validationException', [$responseCustom, 'responseValidationException']);
        Response::macro('dataWithLog', [$responseCustom, 'responseDataWithLog']);
    }
}
