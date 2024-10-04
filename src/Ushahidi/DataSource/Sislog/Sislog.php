<?php

namespace Ushahidi\DataSource\Sislog;

/**
 * Sislog Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Sislog
 * @copyright  2024 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Contracts\CallbackDataSource;
use Ushahidi\DataSource\Contracts\OutgoingDataSource;
use Ushahidi\DataSource\Concerns\MapsInboundFields;

class Sislog implements CallbackDataSource, OutgoingDataSource
{
    use MapsInboundFields;

    protected $config;

    /**
     * Client to talk to the sislog API
     *
     * @var \Vonage\Message\Client
     */
    private $client;
    
    protected $defaultServer = '';

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
        return 'Sislog';
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
                'description' => 'The URL where the Sislog server is installed, i.e. https://server.url.com/',
                'rules' => ['required']
            ],
            'from' => [
                'label' => 'From',
                'input' => 'text',
                'description' => 'The from number',
                'rules' => ['required']
            ],
            'api_username' => [
                'label' => 'Username',
                'input' => 'text',
                'description' => 'The API username',
                'rules' => ['required']
            ],
            'api_password' => [
                'label' => 'Password',
                'input' => 'text',
                'description' => 'The API password',
                'rules' => ['required']
            ],
            // 'api_secret' => [
            //     'label' => 'API secret',
            //     'input' => 'text',
            //     'description' => 'The API secret',
            //     'rules' => ['required']
            // ]
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
        if (!isset($this->config['api_username']) || !isset($this->config['api_password'])) {
            Log::warning('Could not send message with Sislog, incomplete config');
            return [MessageStatus::FAILED, false];
        }

        $auth = 'Authorization: Basic ' . base64_encode("$this->config['api_username']:$this->config['api_password']");
       
       
          // Prepare data to send to frontline cloud
        $data = [
            "From" => "",
            "To" => $to,
            "Content" => $message
           // "ClientReference" => $message
        ];

        // Make a POST request to send the data to frontline cloud

        try {
            $response = $this->client->request('POST', $serverUrl . $this->apiUrl, [
                'headers' => [
                    'Accept'               => 'application/json',
                    'Content-Type'         => 'application/json',
                    'Authorization'         => $auth
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
        } catch (\GuzzleHttp\Exception\ClientException | \GuzzleHttp\Exception\RequestException $e) {
            // Log warning to log file.
            Log::warning(
                'Could not make a successful POST request',
                ['message' => $e->getMessage()]
            );
        }
    }

    public static function registerRoutes(Router $router)
    {
        $router->post('sms/sislog', 'Ushahidi\DataSource\Sislog\SislogController@handleRequest');
    }

    public function verifySecret($secret)
    {
        if (isset($this->config['secret']) and $secret === $this->config['secret']) {
            return true;
        }

        return false;
    }
}
