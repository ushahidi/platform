<?php

namespace Ushahidi\App\DataSource\AfricasTalking;

/**
 * AfricasTalking Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\AfricasTalking
 * @copyright  2018 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\CallbackDataSource;
use Ushahidi\App\DataSource\OutgoingAPIDataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\Core\Entity\Contact;
use Log;

class AfricasTalking implements CallbackDataSource, OutgoingAPIDataSource
{

	protected $config;

	/**
	 * Constructor function for DataSource
	 */
	public function __construct(array $config, \GuzzleHttp\Client $client = null)
	{
		$this->config = $config;
		$this->client = $client;
	}

	public function getName()
    {
		return 'AfricasTalking';
	}

	public function getId()
	{
		return strtolower($this->getName());
	}

	public function getServices()
	{
		return [MessageType::SMS];
	}

	public function getOptions()
	{
		return array(
			'from' => array(
				'label' => 'From',
				'input' => 'text',
				'description' => 'The from number',
				'rules' => array('required')
			),
			'api_key' => array(
				'label' => 'API Key',
				'input' => 'text',
				'description' => 'The API key',
				'rules' => array('required')
			),
			'api_secret' => array(
				'label' => 'API secret',
				'input' => 'text',
				'description' => 'The API secret',
				'rules' => array('required')
			),
			'username' => array(
				'label' => 'Africa\'s Talking username',
				'input' => 'text',
				'description' => 'Africa\'s Talking username',
				'rules' => array('required')
			)
		);
	}

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::PHONE;

	// Africa's Talking api url
	protected $apiUrl = 'https://api.africastalking.com/version1/messaging';

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		// Check we have the required config
		if (!isset($this->config['api_key']) || !isset($this->config['username'])) {
			app('log')->warning('Could not send message with Africa\'s Talking, incomplete config');
			return array(MessageStatus::FAILED, false);
		}


		try {

			$headers = [
				'Accept' => 'application/json',
				'Apikey' => $this->api_key,
			];
       
			$body = [
				'from'     => $this->config['from'],
				'username' => $this->config['username'],
				'message'  => $message,
				'to'       => $to
			];

			$response = $this->client->request('POST', $this->apiUrl, [
				'headers'     => $headers,
				'form_params' => $body,
			]);
			// Successfully executed the request
			if ($response->getStatusCode() === 200) {
				return array(MessageStatus::SENT, false);
			}

			// Log warning to log file.
			$status = $response->getStatusCode();
			app('log')->warning(
                'Could not make a successful POST request',
                array('message' => $response->messages[$status], 'status' => $status)
            );
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			// Log warning to log file.
			app('log')->warning(
                'Could not make a successful POST request',
                array('message' => $e->getMessage())
            );
		}

		return array(MessageStatus::FAILED, false);
	}

	public function registerRoutes(\Laravel\Lumen\Routing\Router $router)
	{
		$router->post('sms/africastalking', 'Ushahidi\App\DataSource\AfricasTalking\AfricasTalkingController@handleRequest');
		$router->post('africastalking', 'Ushahidi\App\DataSource\AfricasTalking\AfricasTalkingController@handleRequest');
	}

	public function verifySecret($secret)
	{
		if (isset($this->config['secret']) and $secret === $this->config['secret']) {
			return true;
		}

		return false;
	}
}
