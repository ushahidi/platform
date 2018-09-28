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
        $this->registerCommands();
    }

    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            $this->app->make('datasources')->registerRoutes();
        }
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
        $manager->addSource($this->makeOutgoingEmail($dataProviderConfig));
        $manager->addSource($this->makeFrontlineSMS($dataProviderConfig));
        $manager->addSource($this->makeNexmo($dataProviderConfig));
        $manager->addSource(new SMSSync\SMSSync($dataProviderConfig['smssync']));
        $manager->addSource($this->makeTwilio($dataProviderConfig));
        $manager->addSource($this->makeTwitter($dataProviderConfig));

        return $manager;
    }

    protected function makeEmail($dataProviderConfig)
    {
        return new Email\Email(
            $dataProviderConfig['email'],
            $this->app->make('mailer'),
            service('site.config'),
            service('clienturl'),
            service('repository.message')
        );
    }

    protected function makeOutgoingEmail($dataProviderConfig)
    {
        return new Email\OutgoingEmail(
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

    protected function makeTwitter($dataProviderConfig)
    {
        return new Twitter\Twitter(
            $dataProviderConfig['twitter'],
            service('repository.config'),
            function ($consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret) {
                return new \Abraham\TwitterOAuth\TwitterOAuth(
                    $consumer_key,
                    $consumer_secret,
                    $oauth_access_token,
                    $oauth_access_token_secret
                );
            }
        );
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

    public function registerCommands()
    {
        $this->commands([
            Console\IncomingCommand::class,
            Console\OutgoingCommand::class,
            Console\ListCommand::class,
        ]);
    }
}
