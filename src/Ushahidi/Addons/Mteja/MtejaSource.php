<?php

namespace Ushahidi\Addons\Mteja;

/**
 * AfricasTalking Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\Mteja
 * @copyright  2022 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Illuminate\Routing\Router;
use Ushahidi\Contracts\DataSource\MessageType;
use Ushahidi\Addons\Mteja\ShortMessageController;
use Ushahidi\DataSource\Concerns\MapsInboundFields;
use Ushahidi\Contracts\DataSource\CallbackDataSource;

class MtejaSource implements CallbackDataSource
{
    use MapsInboundFields;

    protected $config;

    /**
     * Constructor function for DataSource
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getName()
    {
        return 'Mteja';
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
        return [];
    }

    public function getInboundFields()
    {
        return [
            'Message' => 'text'
        ];
    }

    public function isUserConfigurable()
    {
        return false;
    }

    public function send($to, $message, $title = "", $contact_type = null)
    {
    }

    public static function registerRoutes(Router $router)
    {
        $router->post('sms/mteja', ShortMessageController::class.'@handleRequest');
    }
}
