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
        $this->registerManager();
        $this->registerDataSources();
        $this->registerRoutes();
    }

    /**
     * Register the data provider manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('datasources', function () {
            return new DataSourceManager($this->app);
        });
    }

    protected function registerDataSources()
    {
        DataSourceManager::addSource('email', new Email\Email);
        DataSourceManager::addSource('frontlinesms', new FrontlineSMS\FrontlineSMS);
        DataSourceManager::addSource('nexmo', new Nexmo\Nexmo);
        DataSourceManager::addSource('smssync', new SMSSync\SMSSync);
        DataSourceManager::addSource('twilio', new Twilio\Twilio);
        DataSourceManager::addSource('twitter', new Twitter\Twitter);
    }

    protected function registerRoutes()
    {
        $this->app->post('/sms/{sms}', [
            'uses' => ''
        ]);
    }
}
