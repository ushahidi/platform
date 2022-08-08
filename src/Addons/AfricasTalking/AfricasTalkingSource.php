<?php

namespace Ushahidi\Addons\AfricasTalking;

/**
 * AfricasTalking Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\AfricasTalking
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Closure;
use Illuminate\Routing\Router;
use AfricasTalking\SDK\AfricasTalking;
use Ushahidi\Contracts\DataSource\MessageType;
use Ushahidi\Contracts\DataSource\MessageStatus;
use Ushahidi\DataSource\Concerns\MapsInboundFields;
use Ushahidi\Contracts\DataSource\CallbackDataSource;
use Ushahidi\Contracts\DataSource\OutgoingDataSource;

class AfricasTalkingSource implements CallbackDataSource, OutgoingDataSource
{
    use MapsInboundFields;

    protected $config;

    protected $clientFactory;

    public function __construct(array $config = [], Closure $clientFactory = null)
    {
        $this->config = $config;

        $this->clientFactory = $clientFactory;
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
        return [MessageType::SMS, MessageType::IVR];
    }

    public function getOptions()
    {
        return [
            'username' => [
                'label' => 'Username',
                'input' => 'text',
                'description' => 'The app username',
                'rules' => ['required']
            ],
            'api_key' => [
                'label' => 'API Key',
                'input' => 'text',
                'description' => 'The app API key',
                'rules' => ['required']
            ],
            'short_code' => [
                'label' => 'SMS Short code',
                'input' => 'text',
                'description' => 'The short code for sending a message via sms channel',
                'rules' => ['required']
            ],
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

    public function send($to, $message, $title = "", $contact_type = null, $message_type = null, $metadata = [])
    {
        $client = is_null($this->clientFactory) ?
            $this->initialize($this->config['username'], $this->config['api_key']) :
            ($this->clientFactory)($this->config['username'], $this->config['api_key']);

        switch ($message_type) {
            case MessageType::SMS:
            case MessageType::USSD: 
                $response = $client->sms()->send([
                    'to' => $to,
                    'from' => trim($this->config['short_code']),
                    'message' => trim($message)
                ]);
                if ($response['status'] == 'success') {
                    $data = [MessageStatus::SENT, $response['data']->SMSMessageData->Recipients[0]->messageId];
                } else {
                    $data = [MessageStatus::FAILED, false];
                }
                break;
            case MessageType::IVR:
                $data = [MessageStatus::UNKNOWN, false]; // An IVR Prompt with a question to ask a question
            default:
                $data = [MessageStatus::FAILED, false];
                break;
        }

        return $data;
    }

    protected function initialize($username, $apiKey)
    {
        return new AfricasTalking($username, $apiKey);
    }

    public static function registerRoutes(Router $router)
    {
        $router->post('sms/africas-talking', ShortMessageController::class . '@handleRequest');
    }
}
