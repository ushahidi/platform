<?php

namespace Ushahidi\App\Tools;

use Ushahidi\Core\Entity\ConfigRepository;

class Features
{
    /**
     * @param array $configRepo
     */
    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * Check if a feature is enabled
     * @param  string  $feature
     * @return boolean
     */
    public function isEnabled($feature)
    {
        $config = $this->configRepo->get('features');

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
        $config = $this->configRepo->get('features');

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
