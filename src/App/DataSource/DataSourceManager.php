<?php

namespace Ushahidi\App\DataSource;

use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Routing\Router;
use Ushahidi\Core\Entity\ConfigRepository;
use InvalidArgumentException;

class DataSourceManager
{
    /**
     * Cache lifetime in minutes
     */
    const CACHE_LIFETIME = 1;

    /**
     * Config repo instance
     *
     * @var \Ushahidi\Core\Entity\ConfigRepository;
     */
    protected $configRepo;

    /**
     * Sources to be configured
     *
     * [name => classname]
     *
     * @var [string, ...]
     */
    protected $sources = [
        'email' => Email\Email::class,
        'outgoingemail' => Email\OutgoingEmail::class,
        'frontlinesms' => FrontlineSMS\FrontlineSMS::class,
        'nexmo' => Nexmo\Nexmo::class,
        'smssync' => SMSSync\SMSSync::class,
        'twilio' => Twilio\Twilio::class,
        'twitter' => Twitter\Twitter::class,
    ];

    /**
     * The array of data sources.
     *
     * @var [Ushahidi\App\DataSource\DataSource, ...]
     */
    protected $loadedSources = [];

    /**
     * Data Source Storage
     * @var
     */
    protected $storage;

    /**
     * Create a new datasource manager instance.
     *
     * @param  Laravel\Lumen\Routing\Router  $router
     * @return void
     */
    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    public function getSources() : array
    {
        return array_keys($this->sources);
    }

    /**
     * Get an array of enabled source names
     *
     * @return [string, ...]
     */
    public function getEnabledSources() : array
    {
        return Cache::remember('datasources.enabled', self::CACHE_LIFETIME, function () {
            // Load enabled sources
            $enabledSources = array_filter(
                $this->configRepo->get('data-provider')->asArray()['providers']
            );

            // Load available sources
            $availableSources = array_filter(
                $this->configRepo->get('features')->asArray()['data-providers']
            );

            $sources = array_intersect_key(
                $this->sources,
                $enabledSources,
                $availableSources
            );

            return array_keys($sources);
        });
    }

    /**
     * Check if source is enabled
     * @param  string  $name
     * @return boolean
     */
    public function isEnabledSource(string $name) : bool
    {
        $sources = $this->getEnabledSources();

        return in_array($name, $sources);
    }

    /**
     * Get data source instance
     * @param  string $name
     * @return DataSource
     */
    public function getSource(string $name) : DataSource
    {
        return $this->loadedSources[$name] ?? $this->resolve($name);
    }

    /**
     * Get enabled data source instance
     * @param  string $name
     * @return DataSource
     */
    public function getEnabledSource(string $name) : DataSource
    {
        if (!$this->isEnabledSource($name)) {
            throw new InvalidArgumentException("Source [{$name}] is not enabled.");
        }

        return $this->getSource($name);
    }

    /**
     * Get the enable source for a specific type
     * @param  string $type
     * @return DataSource|boolean
     */
    public function getSourceForType(string $type)
    {
        // Grab the first enabled source that provides that service
        foreach ($this->getEnabledSources() as $name) {
            $source = $this->getSource($name);
            if (in_array($type, $source->getServices())) {
                return $source;
            }
        }

        return false;
    }

    /**
     * Register routes for callback sources
     */
    public function registerRoutes(Router $router)
    {
        foreach ($this->sources as $name => $class) {
            if (in_array(CallbackDataSource::class, class_implements($class))) {
                $class::registerRoutes($router);
            }
        }
    }

    /**
     * Set data source storage
     * @param DataSourceStorage $storage
     */
    public function setStorage(DataSourceStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Get data source storage
     * @return DataSourceStorage
     */
    public function getStorage() : DataSourceStorage
    {
        return $this->storage;
    }

    /**
     * Get config for data source
     * @param  string $name
     * @return array
     */
    protected function getConfig(string $name) : array
    {
        $config = Cache::remember('config.data-provider', self::CACHE_LIFETIME, function () {
            return $this->configRepo->get('data-provider')->asArray();
        });

        return $config[$name] ?? [];
    }

    /**
     * Resolve data source class
     * @param  string $name
     * @return DataSource
     */
    protected function resolve(string $name) : DataSource
    {
        $config = $this->getConfig($name);

        $driverMethod = 'create'.ucfirst($name).'Source';

        if (method_exists($this, $driverMethod)) {
            return $this->loadedSources[$name] = $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Source [{$name}] is not supported.");
        }
    }

    /**
     * Wipe resolved source instances
     */
    public function clearResolvedSources()
    {
        $this->loadedSources = [];
    }

    protected function createSmssyncSource(array $config)
    {
        return new SMSSync\SMSSync($config);
    }

    protected function createEmailSource(array $config)
    {
        return new Email\Email(
            $config,
            app('mailer'),
            app(\Ushahidi\Core\Entity\MessageRepository::class)
        );
    }

    protected function createOutgoingemailSource(array $config)
    {
        return new Email\OutgoingEmail(
            $config, // NB: This is not the same as email config and is likely to be empty
            app('mailer')
        );
    }

    protected function createFrontlinesmsSource(array $config)
    {
        return new FrontlineSMS\FrontlineSMS($config, new \GuzzleHttp\Client());
    }

    protected function createTwilioSource(array $config)
    {
        return new Twilio\Twilio($config, function ($accountSid, $authToken) {
            return new \Twilio\Rest\Client($accountSid, $authToken);
        });
    }

    protected function createNexmoSource(array $config)
    {
        return new Nexmo\Nexmo($config, function ($apiKey, $apiSecret) {
            return new \Nexmo\Client(new \Nexmo\Client\Credentials\Basic($apiKey, $apiSecret));
        });
    }

    protected function createTwitterSource($config)
    {
        return new Twitter\Twitter(
            $config,
            $this->configRepo,
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
}
