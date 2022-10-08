<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

trait DefaultData
{
    protected function addDefaultDataToArray($data)
    {
        // We can't define the method getDefaultData in this trait
        // due to the way method overriding works with trait inheritance.
        // The class using this trait can override the method,
        // but a subclass of that class cannot.
        if (method_exists($this, 'getDefaultData')) {
            // fill in available defaults for any missing values
            foreach ($this->getDefaultData() as $key => $default_value) {
                if (!isset($data[$key])) {
                    $data[$key] = $default_value;
                }
            }
        }

        return $data;
    }
}
