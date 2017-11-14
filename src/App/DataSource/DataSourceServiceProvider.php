<?php

namespace Ushahidi\App\DataSource;

use Illuminate\Support\ServiceProvider;

class DataSourceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerStorage();
        $this->registerManager();
        $this->registerRoutes();
    }

    /**
     * Register the data provider manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('datasources', function ($app) {
            $manager = new DataSourceManager($app->router);

            $configRepo = service('repository.config');
            $dataProviderConfig = $configRepo->get('data-provider')->asArray();

            $manager->setEnabledSources($dataProviderConfig['providers']);
            $manager->setAvailableSources(service('features.data-providers'));

            $manager->setStorage($app->make(DataSourceStorage::class));

            $this->registerDataSources($manager);
            $manager->registerRoutes();

            return $manager;
        });

        $this->app->singleton(DataSourceManager::class, function ($app) {
            return $app->make('datasources');
        });
    }

    protected function registerDataSources($manager)
    {
        $configRepo = service('repository.config');
        $dataProviderConfig = $configRepo->get('data-provider')->asArray();

        $manager->addSource($this->makeEmail($dataProviderConfig));
        $manager->addSource($this->makeFrontlineSMS($dataProviderConfig));
        $manager->addSource($this->makeNexmo($dataProviderConfig));
        $manager->addSource(new SMSSync\SMSSync($dataProviderConfig['smssync']));
        $manager->addSource($this->makeTwilio($dataProviderConfig));
        $manager->addSource(new Twitter\Twitter($dataProviderConfig['twitter']));

        return $manager;
    }

    protected function makeEmail($dataProviderConfig)
    {
        return new Email\Email(
            $dataProviderConfig['email'],
            $this->app->make('mailer'),
            service('site.config'),
            service('clienturl')
        );
    }

    protected function makeFrontlineSMS($dataProviderConfig)
    {
        return new FrontlineSMS\FrontlineSMS($dataProviderConfig['frontlinesms'], new \GuzzleHttp\Client());
    }

    protected function makeTwilio($dataProviderConfig)
    {
        return new Twilio\Twilio($dataProviderConfig['twilio'], function ($accountSid, $authToken) {
            return new \Twilio\Rest\Client($accountSid, $authToken);
        });
    }

    protected function makeNexmo($dataProviderConfig)
    {
        return new Nexmo\Nexmo($dataProviderConfig['nexmo'], function ($apiKey, $apiSecret) {
            return new \Nexmo\Client(new \Nexmo\Client\Credentials\Basic($apiKey, $apiSecret));
        });
    }

    protected function registerStorage()
    {
        $this->app->singleton(DataSourceStorage::class, function ($app) {
            return $this->makeStorage();
        });
    }

    protected function makeStorage()
    {
        $receiveUsecase = service('factory.usecase')->get('messages', 'receive');
        $messageRepo = service('repository.message');
        return new DataSourceStorage($receiveUsecase, $messageRepo);
    }

    public function registerRoutes()
    {
        // $this->app->router->post('/sms/{source}[/]', 'Ushahidi\App\DataSource\DataSourceController@handleRequest');
        // $this->app->router->get('/sms/{source}[/]', 'Ushahidi\App\DataSource\DataSourceController@handleRequest');
    }
}
