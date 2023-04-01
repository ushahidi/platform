<?php

namespace Ushahidi\DataSource;

use Closure;
use InvalidArgumentException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Ushahidi\DataSource\Contracts\DataSource;
use Ushahidi\DataSource\Contracts\CallbackDataSource;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Contracts\Repository\Entity\MessageRepository;

class DataSourceManager
{
    /**
     * Cache lifetime in seconds
     */
    const CACHE_LIFETIME = 60;

    /**
     * Config repo instance
     *
     * @var \Ushahidi\Contracts\Repository\Entity\ConfigRepository|null;
     */
    protected $configRepo;

    /**
     * Default Sources to be configured
     *
     *
     * @var \Ushahidi\DataSource\Contracts\DataSource[]
     */
    protected $defaultSources = [
        'email' => Email\Email::class,
        'outgoingemail' => Email\OutgoingEmail::class,
        'frontlinesms' => FrontlineSMS\FrontlineSMS::class,
        'nexmo' => Nexmo\Nexmo::class,
        'smssync' => SMSSync\SMSSync::class,
        'twilio' => Twilio\Twilio::class,
        'twitter' => Twitter\Twitter::class,
    ];

    /**
     * Custom sources to be configured.
     *
     * @var \Ushahidi\DataSource\Contracts\DataSource[]
     */
    protected $customSources = [];

    /**
     * The registered custom datasource resolver.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of data sources.
     *
     * @var \Ushahidi\DataSource\Contracts\DataSource[]
     */
    protected $sources = [];

    /**
     * Data Source Storage
     * @var
     */
    protected $storage;

    /**
     * Create a new datasource manager instance.
     *
     * @param  \Ushahidi\Contracts\Repository\Entity\ConfigRepository  $configRepo
     * @return void
     */
    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    public function getSources(): array
    {
        return array_keys(array_merge($this->defaultSources, $this->customSources));
    }

    /**
     * Get an array of enabled source names
     *
     * @return string[]
     */
    public function getEnabledSources(): array
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

            $allSources = array_merge($this->defaultSources, $this->customSources);

            $sources = array_intersect_key(
                $allSources,
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
    public function isEnabledSource(string $name): bool
    {
        $sources = $this->getEnabledSources();

        return in_array($name, $sources);
    }

    /**
     * Get data source instance
     * @param  string $name
     * @return DataSource
     */
    public function getSource(string $name): Datasource
    {
        return $this->sources[$name] ?? $this->source($name);
    }

    /**
     * Get enabled data source instance
     * @param  string $name
     * @return DataSource
     */
    public function getEnabledSource(string $name): DataSource
    {
        if (!$this->isEnabledSource($name)) {
            throw new InvalidArgumentException("Source [{$name}] is not enabled.");
        }

        return $this->getSource($name);
    }

    /**
     * Get the enabled source for a specific type
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
        /** @var \Ushahidi\DataSource\Contracts\CallbackDataSource $class */
        foreach (array_values(array_merge($this->defaultSources, $this->customSources)) as $class) {
            if (!in_array(CallbackDataSource::class, class_implements($class))) {
                continue;
            }
            $class::registerRoutes($router);
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
    public function getStorage(): DataSourceStorage
    {
        return $this->storage;
    }

    /**
     * Get config for data source
     * @param  string $name
     * @return array
     */
    protected function getConfig(string $name): array
    {
        $config = Cache::remember('config.data-provider', self::CACHE_LIFETIME, function () {
            return $this->configRepo->get('data-provider')->asArray();
        });

        return $config[$name] ?? [];
    }

    /**
     * Get a data source instance
     * @param string  $source
     * @return DataSource
     */
    public function source(string $source): DataSource
    {
        $config = $this->getConfig($source);

        // We'll check to see if a creator method exists for the given source. If not we
        // will check for a custom source creator, which allows developers to create
        // sources using their own customized source creator Closure to create it.
        if (isset($this->customCreators[$source])) {
            return $this->sources[$source] = call_user_func($this->customCreators[$source], $config);
        } elseif (isset($this->customSources[$source])) {
            return $this->sources[$source] = new $this->customSources[$source]($config);
        } else {
            $method = 'create' . ucfirst($source) . 'Source';

            if (method_exists($this, $method)) {
                return $this->sources[$source] = $this->{$method}($config);
            }
        }

        throw new InvalidArgumentException("Source [{$source}] not supported.");
    }

    /**
     * Wipe resolved source instances
     */
    public function clearResolvedSources()
    {
        $this->sources = [];
    }

    /**
     * Register a custom data source Closure.
     *
     * @param  string   $name
     * @param  string   $class
     * @param  Closure  $callback
     * @return $this
     */
    public function extend(string $name, string $class, Closure $callback = null)
    {
        $this->customSources[$name] = $class;

        if (!is_null($callback)) {
            $this->customCreators[$name] = $callback;
        }

        return $this;
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
            app(MessageRepository::class)
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
            return new \Vonage\Client(new \Vonage\Client\Credentials\Basic($apiKey, $apiSecret));
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
