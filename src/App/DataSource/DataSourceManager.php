<?php

namespace Ushahidi\App\DataSource;

class DataSourceManager
{

    /**
     * Router instance
     *
     * @var \Laravel\Lumen\Routing\Router
     */
    protected $router;

    /**
     * The array of data sources.
     *
     * @var [Ushahidi\App\DataSource\DataSource, ...]
     */
    protected $sources = [];

    /**
     * The array of enabled data sources (by name)
     *
     * @var [string, ...]
     */
    protected $enabledSources = [];

    /**
     * The array of available data sources (by name)
     *
     * Availability is defined by feature toggles
     *
     * @var [string, ...]
     */
    protected $availableSources = [];

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
    public function __construct(\Laravel\Lumen\Routing\Router $router)
    {
        $this->router = $router;
    }

    public function addSource(DataSource $source)
    {
        $this->sources[$source->getId()] = $source;
    }

    public function getSource($name = false)
    {
        if ($name) {
            return isset($this->sources[$name]) ? $this->sources[$name] : false;
        }

        return $this->sources;
    }

    public function setEnabledSources(array $sources)
    {
        $this->enabledSources = array_keys(array_filter($sources));
    }


    public function setAvailableSources(array $sources)
    {
        $this->availableSources = array_keys(array_filter($sources));
    }

    public function getEnabledSources($name = false)
    {
        $sources = array_intersect_key(
            $this->sources,
            array_combine($this->enabledSources, $this->enabledSources),
            array_combine($this->availableSources, $this->availableSources)
        );

        if ($name) {
            return isset($sources[$name]) ? $sources[$name] : false;
        }

        return $sources;
    }

    public function getSourceForType($type)
    {
        // Grab the first enabled source that provides that service
        foreach ($this->getEnabledSources() as $source) {
            if (in_array($type, $source->getServices())) {
                return $source;
            }
        }

        return false;
    }

    public function registerRoutes()
    {
        foreach ($this->getEnabledSources() as $source) {
            if (!($source instanceof CallbackDataSource)) {
                // Data source doesn't implement callbacks
                continue;
            }

            $source->registerRoutes($this->router);
        }
    }

    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    public function getStorage()
    {
        return $this->storage;
    }
}
