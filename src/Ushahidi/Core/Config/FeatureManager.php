<?php

namespace Ushahidi\Core\Config;

use Illuminate\Support\Facades\Cache;
use Ushahidi\Core\Entity\ConfigRepository;

class FeatureManager
{
    protected $configRepo;

    /**
     * Cache lifetime in seconds
     */
    const CACHE_LIFETIME = 60;

    /**
     * Cache lifetime in seconds
     */
    const DEFAULT_CACHE_LIFETIME = 60;

    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    protected function getFeatureConfig()
    {
        return Cache::remember('config.features', self::DEFAULT_CACHE_LIFETIME, function () {
            return $this->configRepo->get('features');
        });
    }

    /**
     * Check if a feature is enabled
     * @param  string  $feature
     * @return bool
     */
    public function isEnabled($feature)
    {
        $config = $this->getFeatureConfig();

        if (isset($config->$feature)) {
            if (! is_array($config->$feature)) {
                return (bool) $config->$feature;
            }

            if (isset($config->$feature['enabled'])) {
                return (bool) $config->$feature['enabled'];
            }
        }

        return false;
    }

    /**
     * Get limit for feature
     * @param string $feature
     * @return int|double
     */
    public function getLimit($feature)
    {
        $config = $this->getFeatureConfig();

        if (isset($config->limits[$feature])) {
            if ($config->limits[$feature] === true) {
                // Return infinity ie. unlimited
                return INF;
            }

            return (int) $config->limits[$feature];
        }

        // Return infinity ie. unlimited
        return INF;
    }
}
