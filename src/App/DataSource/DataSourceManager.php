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

    public function addSource($name, DataSource $source)
    {
        $this->sources[$name] = $source;
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
        // If a default source is defined, use that
        if ($this->defaultSources[$type]) {
            return $this->sources[$this->defaultSources[$type]];
        }

        // Otherwise, grab the first enabled source that
        // provides that service
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

    /**
     * Process pending messages for source
     *
     * For services where we can push messages (rather than being polled like SMS Sync):
     * this should grab pending messages and pass them to send()
     *
     * @param  boolean $limit   maximum number of messages to send at a time
     * @todo   move to help class??
     */
    public static function processPendingMessages($limit = 20, $source = false)
    {
        $message_repo = service('repository.message');
        $contact_repo = service('repository.contact');
        $sources = array();
        $count = 0;

        // Grab latest messages
        $messages = $message_repo->getPendingMessages(Message\Status::PENDING, $source, $limit);

        foreach ($messages as $message) {
            // Grab the message source
            if ($message->data_provider) {
                $source = $this->getSource($message->data_provider);
            // Or if the message doesn't have a source set, use the default for this message type
            } else {
                $source = $this->getSourceForType($message->type);
            }

            // Load contact
            $contact = $contact_repo->get($message->contact_id);

            // Send message and get new status/tracking id
            list($new_status, $tracking_id) = $source->send($contact->contact, $message->message, $message->title);

            // Update message details
            $message->setState([
                    'status' => $new_status,
                    'data_provider' => $source->getName(),
                    'data_provider_message_id' => $tracking_id ?: null
                ]);

            // @todo handle errors
            $message_repo->update($message);

            $count ++;
        }

        return $count;
    }
}
