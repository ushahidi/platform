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
}
