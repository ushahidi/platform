<?php

/**
 * Ushahidi Platform DB resolver
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Multisite;

use Ohanzee\Database;

class OhanzeeResolver
{
    /**
     * @var Ohanzee\Database
     */
    protected $currentConnection;

    public function useDefaultConnection()
    {
        $config = config('ohanzee-db'); // In construct() ??

        $this->currentConnection = Database::instance('default', $config['default']);
    }

    public function setConnection($name, $config)
    {
        $defaults = config('ohanzee-db')['default']; // In construct() ??

        $defaults['connection']['hostname'] = $config['host'];
        $defaults['connection']['database'] = $config['database'];
        $defaults['connection']['username'] = $config['username'];
        $defaults['connection']['password'] = $config['password'];

        // @todo check if config already exists
        $this->currentConnection = Database::instance($name, $defaults);
    }

    public function connection()
    {
        if (!$this->currentConnection) {
            throw new \RuntimeException('Database not configured yet');
        }

        return $this->currentConnection;
    }
}
