<?php

/**
 * Ushahidi Core Validation Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\ValidationEngine;

class Ushahidi_ValidationEngine extends \Validation implements ValidationEngine
{
    public function setData(Array $data)
    {
        $this->_data = $data;
    }

    public function getData($key = null)
    {
        if ($key === null) {
            return $this->_data;
        }

        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        return null;
    }
}
