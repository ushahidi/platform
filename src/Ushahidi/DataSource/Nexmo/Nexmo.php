<?php

namespace Ushahidi\DataSource\Nexmo;

/**
 * Nexmo Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Nexmo
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Contracts\CallbackDataSource;
use Ushahidi\DataSource\Contracts\OutgoingDataSource;
use Ushahidi\DataSource\Concerns\MapsInboundFields;

class Nexmo implements CallbackDataSource, OutgoingDataSource
{
    use MapsInboundFields;

    protected $config;

    /**
     * Client to talk to the Nexmo API
     *
     * @var \Vonage\Message\Client
     */
    private $client;

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
        return 'Nexmo';
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
                'label' => 'From',
                'input' => 'text',
                'description' => 'The from number',
                'rules' => ['required']
            ],
            'api_key' => [
                'label' => 'API Key',
                'input' => 'text',
                'description' => 'The API key',
                'rules' => ['required']
            ],
            'api_secret' => [
                'label' => 'API secret',
                'input' => 'text',
                'description' => 'The API secret',
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
        if (!isset($this->config['api_key']) || !isset($this->config['api_secret'])) {
            Log::warning('Could not send message with Nexmo, incomplete config');
            return [MessageStatus::FAILED, false];
        }

        // Make twilio client
        $client = ($this->clientFactory)($this->config['api_key'], $this->config['api_secret']);

        if (!($client instanceof \Vonage\Client)) {
            throw new \Exception("Client is not an instance of Nexmo\Client");
        }

        $from = isset($this->config['from']) ? $this->config['from'] : 'Ushahidi';

        // Send!
        try {
            $message = $client->sms()->send(
                new \Vonage\SMS\Message\SMS(
                    $to,
                    $from,
                    $message
                )
            );

            return [MessageStatus::SENT, $message->current()->getMessageId()];
        } catch (\Throwable $e) {
            Log::warning($e->getMessage());
        }

        return [MessageStatus::FAILED, false];
    }

    public static function registerRoutes(Router $router)
    {
        $router->post('sms/nexmo', 'Ushahidi\DataSource\Nexmo\NexmoController@handleRequest');
        $router->get('sms/nexmo', 'Ushahidi\DataSource\Nexmo\NexmoController@handleRequest');
        $router->get('sms/nexmo/reply', 'Ushahidi\DataSource\Nexmo\NexmoController@handleRequest');
        $router->post('sms/nexmo/reply', 'Ushahidi\DataSource\Nexmo\NexmoController@handleRequest');
        $router->post('nexmo', 'Ushahidi\DataSource\Nexmo\NexmoController@handleRequest');
    }
}
