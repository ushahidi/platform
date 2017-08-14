<?php

namespace Ushahidi\App\DataSource;

class DataSourceManager {

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of data sources.
     *
     * @var array
     */
    protected $sources = [];

    /**
     * The array of enabled data sources (by name)
     *
     * @var array
     */
    protected $enabledSources = [];

    /**
     * The array of available data sources (by name)
     *
     * Availability is defined by feature toggles
     *
     * @var array
     */
    protected $availableSources = [];

    /**
     * Create a new datasource manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function addSource($name, DataSource $source)
    {
        $this->sources[$name] = $source;
    }

    public function getSource($name = false)
    {
        if ($name)
        {
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

    public function getEnabledSources()
    {
        return array_intersect_key($this->sources, array_combine($this->enabledSources, $this->enabledSources), array_combine($this->availableSources, $this->availableSources));
    }

    public function getProviderForType($type)
    {
        // If a default source is defined, use that
        if ($this->defaultSources[$type])
        {
            return $this->sources[$this->defaultSources[$type]];
        }

        // Otherwise, grab the first enabled source that
        // provides that service
        foreach ($this->getEnabledSources() as $source)
        {
            if (in_array($type, $source->getServices()))
            {
                return $source;
            }
        }

        return false;
    }

    /**
     * Process pending messages for provider
     *
     * For services where we can push messages (rather than being polled like SMS Sync):
     * this should grab pending messages and pass them to send()
     *
     * @param  boolean $limit   maximum number of messages to send at a time
     * @todo   move to help class??
     */
    public static function processPendingMessages($limit = 20, $provider = FALSE)
    {
        $message_repo = service('repository.message');
        $contact_repo = service('repository.contact');
        $providers = array();
        $count = 0;

        // Grab latest messages
        $pings = $message_repo->getPendingMessages(Message\Status::PENDING, $provider, $limit);

        foreach($pings as $message)
        {
            $provider = DataSource::factory($message->data_provider, $message->type);

            // Load contact
            $contact = $contact_repo->get($message->contact_id);

            // Send message and get new status/tracking id
            list($new_status, $tracking_id) = $provider->send($contact->contact, $message->message, $message->title);

            // Update message details
            $message->setState([
                    'status' => $new_status,
                    'data_provider' => $provider->provider_name(),
                    'data_provider_message_id' => $tracking_id ?: null
                ]);

            // @todo handle errors
            $message_repo->update($message);

            $count ++;
        }

        return $count;
    }
    /**
     * Get queued outgoing messages
     *
     * @param  boolean $limit   maximum number of messages to return
     * @param  mixed   $current_status  Current status of messages
     * @param  mixed   $new_status  New status to save for message, FALSE to leave status as is
     * @return array            array of messages to be sent.
     *                          Each element in the array should have 'to' and 'message' fields
     * @todo   move to help class??
     */
    public function getPendingMessages($limit = FALSE, $current_status = Message\Status::PENDING_POLL, $new_status = Message\Status::UNKNOWN)
    {
        $message_repo = service('repository.message');
        $contact_repo = service('repository.contact');
        $messages = array();
        $provider = $this->provider_name;

        // Get All "Sent" SMSSync messages
        // Limit it to 20 MAX and FIFO
        $pings = $message_repo->getPendingMessages($current_status, $provider, $limit);

        foreach ($pings as $message)
        {
            $contact = $contact_repo->get($message->contact_id);
            $messages[] = array(
                'to' => $contact->contact, // @todo load this in the message?
                'message' => $message->message,
                'message_id' => $message->id
                );

            // Update the message status
            if ($new_status)
            {
                $message->setState([
                        'status' => $new_status
                    ]);
                $message_repo->update($message);
            }
        }

        return $messages;
    }

}
