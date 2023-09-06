<?php

namespace Ushahidi\Addons\Infobip\InfobipSMS;

/**
 * Infobip SMS Data Source
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\Infobip
 * @copyright  2023 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Illuminate\Routing\Router;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Concerns\MapsInboundFields;
use Ushahidi\DataSource\Contracts\CallbackDataSource;
use Ushahidi\DataSource\Contracts\OutgoingDataSource;
use Illuminate\Support\Facades\Log;

class InfobipSMS implements CallbackDataSource, OutgoingDataSource
{
    use MapsInboundFields;

    protected $config;

    protected $client;

    public function __construct(array $config = [])
    {
        $this->config = $config;

        $this->client = new \GuzzleHttp\Client();
    }

    public function getName()
    {
        return 'InfoBip-SMS';
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
            'api_base_url' => [
                'label' => 'API Base URL',
                'input' => 'text',
                'description' => 'To see your base URL, login to your Infobip dashboard and you should see it in this format
                  i.e xxxxx.api.infobip.com.',
                'rules' => ['required']
            ],
            'api_key' => [
                'label' => 'API Key',
                'input' => 'text',
                'description' => 'The API key to be used when sending requests to the infobip server',
                'rules' => ['required']
            ],
            'phone_number' => [
                'label' => 'SMS Phone number',
                'input' => 'text',
                'description' => 'The phone number which is registered for sending and receiving SMS messages',
                'rules' => ['required']
            ],
            'signing_key' => [
                'label' => 'Signing Key',
                'input' => 'text',
                'description' => 'Set a secret so that it is used to verify the webhook sent from httpSMS server.
				  You need to configure the same secret when setting up a webhook in your httpSMS settings dashboard.',
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

    public function send($to, $message, $title = "", $contact_type = null, $message_type = null, $metadata = [])
    {
        $uri = $this->config['server_url'] . '/v1/messages/send';
        $apiKey = $this->config['api_key'];
        $from = $this->config['phone_number'];

        $res = $this->client->request('POST', $uri, [
            'headers' => [
                'x-api-key' => $apiKey,
            ],
            'json' => [
                'content' => $message,
                'from' => $from,
                'to' => $to
            ]
        ]);

        $content = json_decode($res->getBody()->getContents(), true);

        if ($content['status'] == 'success') {
            return [MessageStatus::SENT, $content['data']['id']];
        } else {
            Log::error('HttpSMS: Failed to send message: ' . $content['message']);
            return [MessageStatus::FAILED, false];
        }
    }

    public function verifyToken($token)
    {
        $decodedToken = JWT::decode($token, new Key($this->config['signing_key'], 'HS256'));

        return false;
    }

    public static function registerRoutes(Router $router)
    {
        $router->post('/http-sms', HttpSMSController::class . '@handleRequest');
    }
}
