<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Tetservice Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\TestService
 * @copyright  2018 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\Core\Entity\Contact;
use GuzzleHttp\Client;

class DataProvider_TestService extends DataProvider {

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::PHONE;

	// Test Service api url
	protected $_api_url = '';

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		$this->client = new GuzzleHttp\Client();
		$uri = $this->_options['api_url'];
		// Prepare data to send to test service
		$data = array(
			"apiKey" => isset($this->_options['key']) ? $this->_options['key'] : '',
			"payload" => array(
				"message" => $message,
				"recipients" => array(
					array(
						"type" => "mobile",
						"value" => $to
					)
				)
			)
		);
		Kohana::$log->add(Log::WARNING, print_r($uri, true));

		$promise = $this->client->requestAsync('POST', $uri, ['form_params' => $data]);

		$response = $promise->wait();
		// $promise->then(function ($response) {
		// 	Kohana::$log->add(Log::WARNING, print_r($response, true));
		// });
		
		if ($response->getStatusCode() === 200)
		{
			{
				return array(Message_Status::SENT, $this->tracking_id(Message_Type::SMS));
			}
		}

		return array(Message_Status::FAILED, FALSE);
	}
}
