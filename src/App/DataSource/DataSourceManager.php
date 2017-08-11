<?php

namespace Ushahidi\App\DataSource;

class DataSource {

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
     * Create a new datasource manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function addSource($name, $source)
    {
    	$this->source[$name] = $source;
    }

    public function getSource($name)
    {

		if ($provider)
		{
			return isset(self::$_providers[$provider]) ? self::$_providers[$provider] : array();
		}

		return self::$_providers;
    }


    public function getEnabledSources()
    {

		$providers = self::get_providers();
		$enabled_provider_keys = array_keys(array_filter(Kohana::$config->load('data-provider')->get('providers')));

		$enabled_providers = array();
		foreach ($enabled_provider_keys as $provider)
		{
			if (isset($providers[$provider]))
			{
				$enabled_providers[$provider] = $providers[$provider];
			}
		}

		return $enabled_providers;
    }

    public function getProviderForType()
    {

		$config = Kohana::$config->load('data-provider');
		$plugin_config = Kohana::$config->load('_plugins');
		$default_providers = $config->get('default_providers');

		if ($default_providers[$type])
		{
			return $default_providers[$type];
		}

		$enabled_providers = $config->get('providers');
		foreach ($enabled_providers as $provider)
		{
			$provider_config = $plugin_config->get($provider);

			if ($provider_config['services'][$type])
			{
				return $provider;
			}
		}

		return FALSE;
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
