<?php

namespace Ushahidi\App\ImportUshahidiV2\ManifestSchemas;

class ImportParameters
{

    /**
     * @var Mappings|null
     */
    public $mappings;

    /**
     * @var Timezones|null
     */
    public $timezones;

    /**
     * @var Settings|null
     */
    public $settings;

    /**
     *  Check whether mappings were provided as part of the import parameters
     */
    public function hasMappings()
    {
        return ($this->mappings != null);
    }

    /**
     * Return category mappings
     */
    public function getCategoryMappings()
    {
        if ($this->hasMappings()) {
            return $this->mappings->categories;
        }
    }

    /**
     * Return form mappings
     */
    public function getFormMappings()
    {
        if ($this->hasMappings()) {
            return $this->mappings->forms;
        }
    }

    /**
     * Return settings
     */
    public function getSettings()
    {
        if ($this->settings != null) {
            return $this->settings;
        } else {
            return Settings::getDefaults();
        }
    }
}
