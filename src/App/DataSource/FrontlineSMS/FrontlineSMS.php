<?php

namespace Ushahidi\App\DataSource\FrontlineSMS;

/**
 * FrontlineSms Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\Core\Entity\Contact;
use Log;
use Illuminate\Http\Request;

class FrontlineSMS implements DataSource
{

	protected $config;

	/**
	 * Constructor function for DataSource
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function getName()
    {
		return 'FrontlineSMS';
	}

	public function getServices()
	{
		return [MessageType::SMS];
	}

	public function getOptions()
	{
		return array(
			'key' => array(
					'label' => 'Key',
					'input' => 'text',
					'description' => 'The API key',
					'rules' => array('required')
			),
			'secret' => array(
				'label' => 'Secret',
				'input' => 'text',
				'description' => 'Set a secret so that only authorized FrontlineCloud accounts can send/recieve message.
					You need to configure the same secret in the FrontlineCloud Activity.',
				'rules' => array('required')
			)
		);
	}

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::PHONE;

	// FrontlineSms Cloud api url
	protected $apiUrl = 'https://cloud.frontlinesms.com/api/1/webhook';

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
		$request = Request::factory($this->apiUrl)
				->method(Request::POST)
				->body(json_encode($data))
				->headers('Content-Type', 'application/json');

		try {
			$response = $request->execute();
			// Successfully executed the request

			if ($response->status() === 200) {
				return array(DataSource\Message\Status::SENT, $this->tracking_id(DataSource\Message\Type::SMS));
			}

			// Log warning to log file.
			$status = $response->status();
			Log::warning('Could not make a successful POST request',
				array('message' => $response->messages[$status], 'status' => $status));
		} catch (Request_Exception $e) {
			// Log warning to log file.
			Log::warning('Could not make a successful POST request',
				array('message' => $e->getMessage()));
		}

		return array(DataSource\Message\Status::FAILED, false);
	}

	// DataSource
	public function fetch($limit = false)
    {
		return false;
	}

	// DataSource
	public function receive(Request $request)
    {
		return false;
	}

	// DataSource
	public function format($messages)
    {
		return false;
	}

	public function registerRoutes($app)
	{
		$app->post('sms/frontlinesms', 'Ushahidi\App\DataSource\FrontlineSMS\Controller\FrontlineSMS@index');
		$app->post('frontlinesms', 'Ushahidi\App\DataSource\FrontlineSMS\Controller\FrontlineSMS@index');
	}

	public function verifySecret($secret)
	{
		if (isset($this->config['secret']) and $secret === $this->config['secret']) {
			return true;
		}

		return false;
	}
}
