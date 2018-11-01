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

    public function setConnection($name, $config)
    {
        // @todo check if config already exists
        $this->currentConnection = Database::instance($name, $config);
    }

    public function connection()
    {
        return $this->currentConnection;
    }
}
