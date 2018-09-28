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

use Ushahidi\App\DataSource\CallbackDataSource;
use Ushahidi\App\DataSource\OutgoingAPIDataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\App\DataSource\Concerns\MapsInboundFields;
use Ushahidi\Core\Entity\Contact;
use Log;

class Nexmo implements CallbackDataSource, OutgoingAPIDataSource
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
     * Client to talk to the Nexmo API
     *
     * @var NexmoMessage
     */
    private $client;

    /**
     * @return mixed
     */
    public function send($to, $message, $title = "")
    {
        // Check we have the required config
        if (!isset($this->config['api_key']) || !isset($this->config['api_secret'])) {
            app('log')->warning('Could not send message with Nexmo, incomplete config');
            return [MessageStatus::FAILED, false];
        }

        // Make twilio client
        $client = ($this->clientFactory)($this->config['api_key'], $this->config['api_secret']);

        if (!($client instanceof \Nexmo\Client)) {
            throw new \Exception("Client is not an instance of Nexmo\Client");
        }

        $from = isset($this->config['from']) ? $this->config['from'] : 'Ushahidi';

        // Send!
        try {
            $message = $client->message()->send([
                'to' => $to,
                'from' => $from,
                'text' => $message
            ]);

            return [MessageStatus::SENT, $message->getMessageId()];
        } catch (\Nexmo\Client\Exception\Exception $e) {
            app('log')->warning($e->getMessage());
        }

        return [MessageStatus::FAILED, false];
    }

    public function registerRoutes(\Laravel\Lumen\Routing\Router $router)
    {
        $router->post('sms/nexmo', 'Ushahidi\App\DataSource\Nexmo\NexmoController@handleRequest');
        $router->get('sms/nexmo', 'Ushahidi\App\DataSource\Nexmo\NexmoController@handleRequest');
        $router->get('sms/nexmo/reply', 'Ushahidi\App\DataSource\Nexmo\NexmoController@handleRequest');
        $router->post('sms/nexmo/reply', 'Ushahidi\App\DataSource\Nexmo\NexmoController@handleRequest');
        $router->post('nexmo', 'Ushahidi\App\DataSource\Nexmo\NexmoController@handleRequest');
    }
}
