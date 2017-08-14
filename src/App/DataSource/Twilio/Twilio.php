<?php

namespace Ushahidi\App\DataSource\Twilio;

/**
 * Twilio Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\Core\Entity\Contact;
use Services_Twilio, Services_Twilio_RestException;
use Log;

class Twilio implements DataSource {

	protected $config;

	/**
	 * Constructor function for DataSource
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function getName() {
		return 'Twilio';
	}

	public function getServices()
	{
		return [MessageType::SMS, MessageType::IVR];
	}

	public function getOptions()
	{
		return array(
			'from' => array(
				'label' => 'Phone Number',
				'input' => 'text',
				'description' => 'The from phone number. A Twilio phone number enabled for the type of message you wish to send. ',
				'rules' => array('required')
			),
			'account_sid' => array(
				'label' => 'Account SID',
				'input' => 'text',
				'description' => 'The unique id of the Account that sent this message.',
				'rules' => array('required')
			),
			'auth_token' => array(
				'label' => 'Auth Token',
				'input' => 'text',
				'description' => '',
				'rules' => array('required')
			),
			'sms_auto_response' => array(
				'label' => 'SMS Auto response',
				'input' => 'text',
				'description' => '',
				'rules' => array('required')
			)
		);
	}

	/**
	 * Client to talk to the Twilio API
	 *
	 * @var Services_Twilio
	 */
	private $_client;

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		if ( ! isset($this->_client))
		{
			$this->_client = new Services_Twilio($this->_options['account_sid'], $this->_options['auth_token']);
		}

		// Send!
		try
		{
			$message = $this->_client->account->messages->sendMessage($this->config['from'], '+'.$to, $message);
			return array(DataSource\Message\Status::SENT, $message->sid);
		}
		catch (Services_Twilio_RestException $e)
		{
			Log::error($e->getMessage());
		}

		return array(DataSource\Message\Status::FAILED, FALSE);
	}

}
