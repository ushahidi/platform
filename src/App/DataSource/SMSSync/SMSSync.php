<?php

namespace Ushahidi\App\DataSource\SMSSync;

/**
 * SMSSync Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\SMSSync
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\CallbackDataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\App\DataSource\Concerns\MapsInboundFields;
use Ushahidi\Core\Entity\Contact;

class SMSSync implements CallbackDataSource
{
    use MapsInboundFields;

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
        return 'SMSSync';
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
            'intro_step1' => [
                'label' => 'Step 1: Download the "SMSSync" app from the Android Market.',
                'input' => 'read-only-text',
                'description' => function () {
                    return 'Scan this QR Code with your phone to download the app from the Android Market
						<img src="'. url('/images/smssync.png') .'" width="150"/>';
                }
            ],
            // @todo figure out how to inject link and fix base url
            'intro_step2' => [
                'label' => 'Step 2: Android App Settings',
                'input' => 'read-only-text',
                'description' => function () {
                    return 'Turn on SMSSync and use the following link as the Sync URL: ' . url('sms/smssync');
                }
            ],
            'secret' => [
                'label' => 'Secret',
                'input' => 'text',
                'description' => 'Set a secret so that only authorized SMSSync devices can send/recieve message.
					You need to configure the same secret in the SMSSync App.',
                'rules' => ['required']
            ]
        ];
    }

    public function getInboundFields()
    {
        return [
            'Message' => 'text',
            'Date' => 'datetime',
        ];
    }

    public function isUserConfigurable()
    {
        return true;
    }

    /**
     * Contact type user for this provider
     */
    public $contact_type = Contact::PHONE;

    public function registerRoutes(\Laravel\Lumen\Routing\Router $router)
    {
        $router->post('sms/smssync', 'Ushahidi\App\DataSource\SMSSync\SMSSyncController@handleRequest');
        $router->post('smssync', 'Ushahidi\App\DataSource\SMSSync\SMSSyncController@handleRequest');
        $router->get('sms/smssync', 'Ushahidi\App\DataSource\SMSSync\SMSSyncController@handleRequest');
        $router->get('smssync', 'Ushahidi\App\DataSource\SMSSync\SMSSyncController@handleRequest');
    }

    public function verifySecret($secret)
    {
        if (isset($this->config['secret']) and $secret === $this->config['secret']) {
            return true;
        }

        return false;
    }

    public function getSecret()
    {
        return isset($this->config['secret']) ? $this->config['secret'] : false;
    }
}
