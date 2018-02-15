<?php defined('SYSPATH') or die('No direct access allowed');

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
abstract class DataProvider_Core {

	/**
	 * Name of the provider
	 * @var string
	 */
	protected $provider_name;

	/**
	 * Authentication parameters for the default Data provider
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = NULL;

	/**
	 * Data Provider instances
	 * @var array
	 */
	public static $instances = array();

	/**
	 * Provider info
	 * @var array
	 */
	protected static $_providers = array();

	/**
	 * Register A Provider
	 *
	 * @param string $name     Provider name
	 * @param array  $params   Provider info
	 */
	public static function register_provider($name, $params)
	{
		if (self::_valid_provider($params, $name))
		{
			self::$_providers[$name] = $params;
			self::$_providers[$name]['id']= $name;
		}
	}

	/**
	 * Get provider info
	 * @param  boolean $provider provider name
	 * @return array             provider info
	 */
	public static function get_providers($provider = FALSE)
	{
		if ($provider)
		{
			return isset(self::$_providers[$provider]) ? self::$_providers[$provider] : array();
		}

		return self::$_providers;
	}

	/**
	 * Get array of enabled provider names
	 *
	 * @return array   names of enabled providers
	 */
	public static function get_enabled_providers()
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

	/**
	 * Validate Provider Parameters
	 *
	 * @param array $params
	 * @return bool valid/invalid
	 */
	protected static function _valid_provider($params, $name)
	{
		if ( ! is_array($params) )
		{
			return FALSE;
		}

		// Validate Name
		if ( ! isset($params['name']) )
		{
			Kohana::$log->add(Log::ERROR, __("':provider' does not have 'name'", array(':provider' => $name)));
			return FALSE;
		}

		// Validate Version
		if ( ! isset($params['version']) )
		{
			Kohana::$log->add(Log::ERROR, __("':provider' does not have 'version'", array(':provider' => $name)));
			return FALSE;
		}

		// Validate Services
		if ( ! isset($params['services']) OR ! is_array($params['services']) )
		{
			Kohana::$log->add(Log::ERROR, __("':provider' does not have 'services' or 'services' is not an array", array(':provider' => $name)));
			return FALSE;
		}

		// Validate Options
		if ( ! isset($params['options']) OR ! is_array($params['options']) )
		{
			Kohana::$log->add(Log::ERROR, __("':provider' does not have 'options' or 'options' is not an array", array(':provider' => $name)));
			return FALSE;
		}

		// Validate Links
		if ( ! isset($params['links']) OR ! is_array($params['links']) )
		{
			Kohana::$log->add(Log::ERROR, __("':provider' does not have 'links' or 'links' is not an array", array(':provider' => $name)));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Creates and returns a new provider.
	 * Provider name must be passed with its' original casing, e.g.
	 *
	 *    $model = DataProvider::factory('Smssync');
	 *
	 * @chainable
	 * @param   string  $provider  Provider name
	 * @param   string  $type      Required provider type
	 * @return  ORM
	 */
	public static function factory($provider_name = NULL, $type = Message_Type::SMS)
	{
		$config = Kohana::$config->load('data-provider');
		// Grab default provider if none passed
		if ( ! $provider_name)
		{
			$provider_name = self::getProviderForType($type);
		}

		if ( ! $provider_name)
		{
			throw new Kohana_Exception("The messaging service is unavailable at this time. No data provider has been configured for use.");
		}

		$provider_name = strtolower($provider_name);

		if ( ! isset(DataProvider::$instances[$provider_name]))
		{
			$enabled_providers = $config->get('providers');

			$class_name = 'DataProvider_'.ucfirst($provider_name);

			if ( ! class_exists($class_name))
			{
				throw new Kohana_Exception(__("Implementation for ':provider' data provider not found",
				    array(":provider" => $provider_name)));
			}

			DataProvider::$instances[$provider_name] = $provider = new $class_name();

			// Check if the provider is a subclass of DataProvider
			if ( ! is_a($provider, 'DataProvider'))
			{
				throw new Kohana_Exception(__("':class' must extend the DataProvider class",
					array(":provider" => $class_name)));
			}
		}

		return DataProvider::$instances[$provider_name];
	}

	/**
	 * Get Provider For a particular message type
	 * @param  string $type Message/Service Type
	 * @return string       Provider name
	 */
	public static function getProviderForType($type)
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
	 * Constructor function for DataProvider
	 */
	public function __construct()
	{
		// Set provider name based on object name
		$this->provider_name = $this->_object_name = strtolower(substr(get_class($this), 13));

		// Get provider options
		$this->options();
	}

	public function provider_name()
	{
		return $this->provider_name;
	}

	/**
	 * Sets the FROM parameter for the provider
	 *
	 * @return int
	 */
	public function from()
	{
		$options = $this->options();
		return isset($options['from']) ? $options['from'] : '';
	}

	/**
	 * Sets the authentication parameters for the provider
	 *
	 * @return array
	 */
	public function options()
	{
		if (empty($this->_options))
		{
			$this->_options = Kohana::$config->load('data-provider')->get($this->provider_name);
		}

		return is_array($this->_options) ? $this->_options : array();
	}

	/**
	 * Generate A Tracking ID for messages
	 *
	 * @param string $type - type of tracking_id
	 * @return string tracking id
	 */
	public static function tracking_id($type = 'email')
	{
		return uniqid($type . php_uname('n'));
	}

	/**
	 * @param  string  to Phone number to receive the message
	 * @param  string  message Message to be sent
	 * @param  string  title   Message title
	 * @return array   Array of message status, and tracking ID.
	 */
	abstract public function send($to, $message, $title = "");

	/**
	 * Receive Messages From Data Provider
	 *
	 * @param  string type    Message type
	 * @param  string from    From contact
	 * @param  string message Received Message
	 * @param  string to      To contact
	 * @param  string title   Received Message title
	 * @param  string data_provider_message_id Message ID
	 * @return void
	 */
	abstract public function receive($type, $from, $message, $to = NULL, $title = NULL, $date = NULL, $data_provider_message_id = NULL, Array $additional_data = NULL);

	/**
	 * Get queued outgoing messages
	 *
	 * Each element in the array should be an array with
	 * 'to' and 'message' fields
	 *
	 * @param  boolean $limit   maximum number of messages to return
	 * @param  mixed   $current_status  Current status of messages
	 * @param  mixed   $new_status  New status to save for message, FALSE to leave status as is
	 * @return array            array of messages to be sent.
	 */
	abstract public function get_pending_messages($limit = FALSE, $current_status = Message_Status::PENDING_POLL, $new_status = Message_Status::UNKNOWN);

	/**
	 * Fetch messages from provider
	 *
	 * For services where we have to poll for message (Twitter, Email, FrontlineSMS) this should
	 * poll the service and pass messages to $this->receive()
	 *
	 * @param  boolean $limit   maximum number of messages to fetch at a time
	 * @return int              number of messages fetched
	 */
	public function fetch($limit = FALSE)
	{
		return 0;
	}

	/**
	 * Process pending messages for provider
	 *
	 * For services where we can push messages (rather than being polled like SMS Sync):
	 * this should grab pending messages and pass them to send()
	 *
	 * @param  boolean $limit   maximum number of messages to send at a time
	 * @param  string  $provider Grab messages for only this provider
	 */
	public static function process_pending_messages($limit = 20, $provider = FALSE)
	{
		return 0;
	}

}
