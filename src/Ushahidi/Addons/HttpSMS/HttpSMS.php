<?php

namespace Ushahidi\Addons\HttpSMS;

/**
 * Infobip Data Source
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\HttpSMS
 * @copyright  2023 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Concerns\MapsInboundFields;
use Ushahidi\DataSource\Contracts\CallbackDataSource;
use Ushahidi\DataSource\Contracts\OutgoingDataSource;

class HttpSMS implements CallbackDataSource, OutgoingDataSource
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
        return 'HttpSMS';
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
            'server_url' => [
                'label' => 'HttpSMS Server URL',
                'input' => 'text',
                'description' => 'The URL where the httpSMS server is installed, i.e. https://api.httpsms.com',
                'rules' => ['required']
            ],
            'api_key' => [
                'label' => 'API Key',
                'input' => 'text',
                'description' => 'The API key to be used when sending requests to the httpSMS server',
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
        $router->post('sms/http-sms', HttpSMSController::class . '@handleRequest');
    }
}
