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
            $dataSourceManager = new DataSourceManager($this->app);

            $configRepo = service('repository.config');
            $dataProviderConfig = $configRepo->get('data-provider')->asArray();

            $dataSourceManager->setEnabledSources($dataProviderConfig['providers']);
            $dataSourceManager->setAvailableSources(service('features.data-providers'));

            // @todo not sure I really need to tap() this?? Copying from queue provider
            return tap($dataSourceManager, function ($manager) {
                return $this->registerDataSources($manager);
            });
        });
    }

    protected function registerDataSources($manager)
    {
        $configRepo = service('repository.config');
        $dataProviderConfig = $configRepo->get('data-provider')->asArray();

        $manager->addSource('email', new Email\Email($dataProviderConfig['email']));
        $manager->addSource('frontlinesms', new FrontlineSMS\FrontlineSMS($dataProviderConfig['frontlinesms']));
        $manager->addSource('nexmo', new Nexmo\Nexmo($dataProviderConfig['nexmo']));
        $manager->addSource('smssync', new SMSSync\SMSSync($dataProviderConfig['smssync']));
        $manager->addSource('twilio', new Twilio\Twilio($dataProviderConfig['twilio']));
        $manager->addSource('twitter', new Twitter\Twitter($dataProviderConfig['twitter']));

        return $manager;
    }

    protected function registerRoutes()
    {
        $this->app->post('/sms/{sms}', [
            'uses' => ''
        ]);
    }
}
