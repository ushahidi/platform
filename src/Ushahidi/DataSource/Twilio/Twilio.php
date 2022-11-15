<?php

namespace Ushahidi\DataSource\Twilio;

/**
 * Twilio Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Illuminate\Routing\Router;
use Ushahidi\Contracts\Contact;
use Illuminate\Support\Facades\Log;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Contracts\CallbackDataSource;
use Ushahidi\DataSource\Contracts\OutgoingDataSource;
use Ushahidi\DataSource\Concerns\MapsInboundFields;

class Twilio implements CallbackDataSource, OutgoingDataSource
{
    use MapsInboundFields;

    protected $config;

    /**
     * Constructor function for DataSource
     */
    public function __construct(array $config, \Closure $clientFactory = null)
    {
        $this->config = $config;
        $this->clientFactory = $clientFactory;
    }

    public function getName()
    {
        return 'Twilio';
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
        return [
            'from' => [
                'label' => 'Phone Number',
                'input' => 'text',
                'description' => 'The from phone number.
					A Twilio phone number enabled for the type of message you wish to send. ',
                'rules' => ['required']
            ],
            'account_sid' => [
                'label' => 'Account SID',
                'input' => 'text',
                'description' => 'The unique id of the Account that sent this message.',
                'rules' => ['required']
            ],
            'auth_token' => [
                'label' => 'Auth Token',
                'input' => 'text',
                'description' => '',
                'rules' => ['required']
            ],
            'sms_auto_response' => [
                'label' => 'SMS Auto response',
                'input' => 'text',
                'description' => '',
                'rules' => ['required']
            ]
        ];
    }

    public function getInboundFields()
    {
        return [
            'Message' => 'text'
        ];
    }

    public function isUserConfigurable()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function send($to, $message, $title = "", $contact_type = null)
    {
        // Check we have the required config
        if (!isset($this->config['account_sid']) || !isset($this->config['auth_token'])) {
            Log::warning('Could not send message with Twilio, incomplete config');
            return [MessageStatus::FAILED, false];
        }

        // Make twilio client
        $client = ($this->clientFactory)($this->config['account_sid'], $this->config['auth_token']);

        if (!($client instanceof \Twilio\Rest\Client)) {
            throw new \Exception("Client is not an instance of Twilio\Rest\Client");
        }

        $from = isset($this->config['from']) ? $this->config['from'] : 'Ushahidi';

        // WhatsApp contacts need 'whatsapp:' before from and to number
        if ($contact_type === Contact::WHATSAPP) {
            $from = "whatsapp:${from}";
            $to = "whatsapp:${to}";
        }

        // Send!
        try {
            $message = $client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $message
                ]
            );
            return [MessageStatus::SENT, $message->sid];
        } catch (\Twilio\Exceptions\RestException $e) {
            Log::error($e->getMessage());
        }

        return [MessageStatus::FAILED, false];
    }

    public static function registerRoutes(Router $router)
    {
        $router->post('sms/twilio', 'Ushahidi\DataSource\Twilio\TwilioController@handleRequest');
        $router->post('sms/twilio/reply', 'Ushahidi\DataSource\Twilio\TwilioController@handleRequest');
    }

    public function verifySid($sid)
    {
        if (isset($this->config['account_sid']) and $sid === $this->config['account_sid']) {
            return true;
        }

        return false;
    }

    public function getSmsAutoResponse()
    {
        return isset($this->config['sms_auto_response']) ? $this->config['sms_auto_response'] : false;
    }
}
