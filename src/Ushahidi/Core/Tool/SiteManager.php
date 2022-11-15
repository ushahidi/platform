<?php

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Entity\Site;
use Illuminate\Support\Facades\Cache;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;

class SiteManager
{
    /**
     * Cache lifetime in minutes
     */
    const DEFAULT_CACHE_LIFETIME = 1;

    protected static $site;

    protected $configRepo;

    /**
     * @var int
     */
    private $cache_lifetime;

    public function __construct(ConfigRepository $configRepo, ?int $cache_lifetime = null)
    {
        $this->configRepo = $configRepo;

        $this->cache_lifetime = $cache_lifetime ?? self::DEFAULT_CACHE_LIFETIME;
    }

    public function instance()
    {
        return static::$site ?? new Site($this->getConfig());
    }

    public function setCacheLifetime($cache_lifetime)
    {
        $this->cache_lifetime = $cache_lifetime;
    }

    public function setDefault(Site $site)
    {
        static::$site = $site;
    }

    public function getConfig()
    {
        /** @var \Ushahidi\Core\Entity\Config */
        $config = Cache::remember(
            'config.site',
            $this->cache_lifetime,
            function () {
                return $this->configRepo->get('site');
            }
        );

        return $config->asArray();
    }

    /**
     * Dynamically call the default site instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->instance()->$method(...$parameters);
    }
}
