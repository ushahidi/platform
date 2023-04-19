<?php

namespace Ushahidi\DataSource;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Core\Usecase\Message\ReceiveMessage;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\MessageRepository;

class DataSourceServiceProvider extends ServiceProvider
{
    protected $defer = true;

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
        $this->app->make('datasources')->registerRoutes($this->app->router);

        Event::listen('site.changed', function () {
            // Reset datasources
            $this->app->make('datasources')->clearResolvedSources();
        });
    }

    /**
     * Register the data provider manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('datasources', function ($app) {
            $configRepo = $this->app->make(ConfigRepository::class);

            $manager = new DataSourceManager($configRepo);
            $manager->setStorage($app->make(DataSourceStorage::class));

            return $manager;
        });

        $this->app->singleton(DataSourceManager::class, function ($app) {
            return $app->make('datasources');
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
        $messageRepo = $this->app->make(MessageRepository::class);
        $contactRepo = $this->app->make(ContactRepository::class);
        $receiveUsecase = $this->app->make(ReceiveMessage::class);
        return new DataSourceStorage($receiveUsecase, $contactRepo, $messageRepo);
    }

    public function registerCommands()
    {
        $this->commands([
            Console\IncomingCommand::class,
            Console\OutgoingCommand::class,
            Console\ListCommand::class,
        ]);
    }

    public function provides()
    {
        return [
            'datasources',
            DataSourceManager::class,
        ];
    }
}
