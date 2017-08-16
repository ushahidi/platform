<?php

namespace Ushahidi\App\DataSource\Nexmo;

/**
 * Nexmo Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Nexmo
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\Core\Entity\Contact;
use Log;

class Nexmo implements DataSource
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
		return 'Nexmo';
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
			'secret' => array(
				'label' => 'Secret',
				'input' => 'text',
				'description' => 'The secret value',
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
			)
		);
	}

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::PHONE;

	/**
	 * Client to talk to the Nexmo API
	 *
	 * @var NexmoMessage
	 */
	private $client;

	/**
	 * Sets the FROM parameter for the provider
	 *
	 * @return int
	 */
	public function from()
	{
		// Get provider phone (FROM)
		// Replace non-numeric
		return preg_replace('/\D+/', "", parent::from());
	}

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		include_once __DIR__ . '/nexmo/NexmoMessage';

		if (! isset($this->client)) {
			$this->client = new \NexmoMessage($this->_options['api_key'], $this->_options['api_secret']);
		}

		// Send!
		try {
			$info = $this->client->sendText('+'.$to, '+'.preg_replace("/[^0-9,.]/", "", $this->from()), $message);
			foreach ($info->messages as $message) {
				if ($message->status != 0) {
					Log::warning('Nexmo: '.$message->errortext);
					return array(DataSource\Message\Status::FAILED, false);
				}

				return array(DataSource\Message\Status::SENT, $message->messageid);
			}
		} catch (Exception $e) {
			Log::warning($e->getMessage());
		}

		return array(DataSource\Message\Status::FAILED, false);
	}

	// DataSource
	public function fetch($limit = false)
    {
		return false;
	}

	// DataSource
	public function receive($request)
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
		$app->post('sms/nexmo', 'Ushahidi\App\DataSource\Nexmo\Controller\Nexmo@reply');
		$app->post('sms/nexmo/reply', 'Ushahidi\App\DataSource\Nexmo\Controller\Nexmo@reply');
		$app->post('nexmo', 'Ushahidi\App\DataSource\Nexmo\Controller\Nexmo@reply');
	}
}
