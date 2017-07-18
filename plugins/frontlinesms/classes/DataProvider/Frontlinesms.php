<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * FrontlineSms Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\Core\Entity\Contact;

class DataProvider_FrontlineSms extends DataProvider {

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::PHONE;

	// FrontlineSms Cloud api url
	protected $_api_url = 'https://cloud.frontlinesms.com/api/1/webhook';

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		// Prepare data to send to frontline cloud
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

		// Make a POST request to send the data to frontline cloud
		$request = Request::factory($this->_api_url)
				->method(Request::POST)
				->body(json_encode($data))
				->headers('Content-Type', 'application/json');

		try
		{
			$response = $request->execute();
			// Successfully executed the request

			if ($response->status() === 200)
			{
				return array(Message_Status::SENT, $this->tracking_id(Message_Type::SMS));
			}

			// Log warning to log file.
			$status = $response->status();
			Kohana::$log->add(Log::WARNING, 'Could not make a successful POST request: :message  status code: :code',
				array(':message' => $response->messages[$status], ':code' => $status));
		}
		catch(Request_Exception $e)
		{
			// Log warning to log file.
			Kohana::$log->add(Log::WARNING, 'Could not make a successful POST request: :message',
				array(':message' => $e->getMessage()));
		}

		return array(Message_Status::FAILED, FALSE);
	}
}
