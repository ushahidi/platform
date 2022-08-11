<?php

namespace Ushahidi\DataSource\FrontlineSMS;

/**
 * FrontlineSms Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Illuminate\Routing\Router;
use Ushahidi\Core\Entity\Contact;
use Illuminate\Support\Facades\Log;
use Ushahidi\DataSource\Contracts\CallbackDataSource;
use Ushahidi\DataSource\Contracts\OutgoingDataSource;
use Ushahidi\DataSource\Concerns\MapsInboundFields;
use Ushahidi\DataSource\Contracts\MessageType as MessageType;
use Ushahidi\DataSource\Contracts\MessageStatus as MessageStatus;

class FrontlineSMS implements CallbackDataSource, OutgoingDataSource
{
    use MapsInboundFields;

    protected $config;

     /**
     * Contact type user for this provider
     */
    public $contact_type = Contact::PHONE;

    // FrontlineSms Cloud api url
    protected $apiUrl = 'api/1/webhook';

    protected $defaultServer = 'https://cloud.frontlinesms.com/';

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
        return 'FrontlineSMS';
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
                'label' => 'FrontlineSMS Server URL',
                'input' => 'text',
                'description' => 'The URL where the FrontlineSMS server is installed, i.e. https://server.url.com/',
                'rules' => ['required']
            ],
            'key' => [
                    'label' => 'Key',
                    'input' => 'text',
                    'description' => 'The API key',
                    'rules' => ['required']
            ],
            'secret' => [
                'label' => 'Secret',
                'input' => 'text',
                'description' => 'Set a secret so that only authorized FrontlineCloud accounts can send/recieve message.
					You need to configure the same secret in the FrontlineCloud Activity.',
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
        // Obtain server url, ensure it ends with '/'
        $serverUrl = $this->config['server_url'] ?? $this->defaultServer;
        if (substr($serverUrl, -1) != '/') {
            $serverUrl = $serverUrl . '/';
        }

        // Check we have the required config
        if (!isset($this->config['key'])) {
            Log::warning('Could not send message with FrontlineSMS, incomplete config');
            return [MessageStatus::FAILED, false];
        }

        // Prepare data to send to frontline cloud
        $data = [
            "apiKey" => isset($this->config['key']) ? $this->config['key'] : '',
            "payload" => [
                "message" => $message,
                "recipients" => [
                    [
                        "type" => "mobile",
                        "value" => $to
                    ]
                ]
            ]
        ];

        // Make a POST request to send the data to frontline cloud

        try {
            $response = $this->client->request('POST', $serverUrl . $this->apiUrl, [
                    'headers' => [
                        'Accept'               => 'application/json',
                        'Content-Type'         => 'application/json'
                    ],
                    'json' => $data
                ]);
            // Successfully executed the request

            if ($response->getStatusCode() === 200) {
                return [MessageStatus::SENT, false];
            }

            // Log warning to log file.
            $status = $response->getStatusCode();
            Log::warning(
                'Could not make a successful POST request',
                ['message' => $response->messages[$status], 'status' => $status]
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Log warning to log file.
            Log::warning(
                'Could not make a successful POST request',
                ['message' => $e->getMessage()]
            );
        }

        return [MessageStatus::FAILED, false];
    }

    public static function registerRoutes(Router $router)
    {
        $router->post('sms/frontlinesms', 'Ushahidi\DataSource\FrontlineSMS\FrontlineSMSController@handleRequest');
        $router->post('frontlinesms', 'Ushahidi\DataSource\FrontlineSMS\FrontlineSMSController@handleRequest');
    }

    public function verifySecret($secret)
    {
        if (isset($this->config['secret']) and $secret === $this->config['secret']) {
            return true;
        }

        return false;
    }
}
