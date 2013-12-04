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
	 * [FROM] Number/Contact
	 * @var array
	 */
	protected $_from = null;

	/**
	 * Authentication parameters for the default Data provider
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Creates and returns a new provider.
	 * Provider name must be passed with its' original casing, e.g.
	 *
	 *    $model = DataProvider::factory('Smssync');
	 *
	 * @chainable
	 * @param   string  $provider  Provider name
	 * @return  ORM
	 */
	public static function factory($provider_name = NULL)
	{
		$config = Kohana::$config->load('data-provider');

		// Grab default provider if none passed
		$provider_name = ($provider_name) ? $provider_name : $config->get('default_provider');
		$provider_name = strtolower($provider_name);

		$enabled_providers = $config->get('providers');
		if ( empty($enabled_providers[$provider_name]) )
		{
			throw new Kohana_Exception("The messaging service is unavailable at this time. No data provider has been configured for use.");
		}

		$class_name = 'DataProvider_'.ucfirst($provider_name);

		if ( ! class_exists($class_name))
		{
			throw new Kohana_Exception(__("Implementation for ':provider' data provider not found",
			    array(":provider" => $provider_name)));
		}

		$provider = new $class_name();

		// Check if the provider is a subclass of DataProvider
		if ( ! is_a($provider, 'DataProvider'))
		{
			throw new Kohana_Exception(__("':class' must extend the DataProvider class",
				array(":provider" => $class_name)));
		}

		return $provider;
	}

	/**
	 * Get array of available provider names
	 * @return array   names of enabled providers
	 */
	public static function get_available_providers()
	{
		$enabled_providers = Kohana::$config->load('data-provider')->get('providers');
		return array_keys(array_filter($enabled_providers));
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

		// Get From
		$this->from();
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
		// Get provider phone (FROM)
		// Replace non-numeric
		$options = $this->options();
		$this->_from = preg_replace("/[^0-9,.]/", "", $options['from']);

		return $this->_from;
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

		return $this->_options;
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
	 */
	abstract public function send($to, $message);

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
	abstract public function receive($type, $from, $message, $to = NULL, $title = NULL, $data_provider_message_id = NULL);

	/**
	 * Get queued outgoing messages
	 *
	 * Each element in the array should be an array with
	 * 'to' and 'message' fields
	 *
	 * @param  boolean $limit   maximum number of messages to return
	 * @return array            array of messages to be sent.
	 */
	abstract public function get_outgoing_messages($limit = FALSE);

}