<?php

namespace Ushahidi\App\Tools;

class Features
{

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Check if a feature is enabled
     * @param  string  $feature
     * @return boolean
     */
    public function isEnabled($feature)
    {
        if (isset($this->config[$feature])) {
            if (!is_array($this->config[$feature])) {
                return !!$this->config[$feature];
            }

            if (isset($this->config[$feature]['enabled'])) {
                return !!$this->config[$feature]['enabled'];
            }
        }

        return false;
    }
}
