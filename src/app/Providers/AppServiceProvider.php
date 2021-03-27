<?php

namespace App\Providers;

use App\Serializers\ApiSerializer;
use App\Services\Base;
use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use League\Fractal\Manager;
use Osi\QueryBuilder\Filter;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('base64max', 'App\Validators\Base64Validator@validateBase64Max', 'The :attribute must be less than 1 MB.');
        Validator::extend('base64mimes', 'App\Validators\Base64Validator@validateBase64Mimes', 'The :attribute must be a file of type: jpg,jpeg,png.');

        $this->app->bind(SesClient::class, function () {
            return new SesClient([
                'credentials' => new Credentials(config('services.ses.key'), config('services.ses.secret')),
                'region' => config('services.ses.region'),
                'version' => 'latest',
            ]);
        });

        $this->app[Base::class] = function ($app) {
            return $app['app.base'];
        };

        $this->app->bind(Manager::class, function (Application $app) {
            $manager = new Manager();
            $manager->setSerializer($app->make(ApiSerializer::class));
            return $manager;
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('app.base', function (Application $app) {
            return new Base(new Filter());
        });
    }
}
