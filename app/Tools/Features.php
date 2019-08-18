<?php

namespace Ushahidi\App\Tools;

use Ushahidi\Core\Entity\ConfigRepository;
use Illuminate\Support\Facades\Cache;

class Features
{
    /**
     * Cache lifetime in minutes
     */
    const CACHE_LIFETIME = 1;

    /**
     * @param array $configRepo
     */
    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    protected function getFeatureConfig()
    {
        return Cache::remember('config.features', self::CACHE_LIFETIME, function () {
            return $this->configRepo->get('features');
        });
    }

    /**
     * Check if a feature is enabled
     * @param  string  $feature
     * @return boolean
     */
    public function isEnabled($feature)
    {
        $config = $this->getFeatureConfig();

        if (isset($config->$feature)) {
            if (!is_array($config->$feature)) {
                return !!$config->$feature;
            }

            if (isset($config->$feature['enabled'])) {
                return !!$config->$feature['enabled'];
            }
        }

        return false;
    }

    /**
     * Get limit for feature
     * @param  string $feature
     * @return int|INF
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
