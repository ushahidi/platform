<?php

namespace Ushahidi\App\DataSource;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        $this->app->make('datasources')->registerRoutes($this->app->router);

        Event::listen('multisite.site.changed', function () {
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
            $configRepo = $this->app->make(\Ushahidi\Core\Entity\ConfigRepository::class);

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
        $receiveUsecase = $this->app->make(\Ushahidi\Factory\UsecaseFactory::class)
            ->get('messages', 'receive');
        $messageRepo = $this->app->make(\Ushahidi\Core\Entity\MessageRepository::class);
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
