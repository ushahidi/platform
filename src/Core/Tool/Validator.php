<?php

/**
 * Ushahidi Platform Validator Interface
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface Validator
{
    /**
     * Check the data
     *
     * @param  Array $data      an array of changed values to check in $key => $value format
     * @param  Array $fullData  an array of full entity data for reference during validation
     * @return bool
     */
    public function check(array $data, array $fullData = []) : bool;

    /**
     * Return an array of any errors that occurred during validation
     *
     * @return Array
     */
    public function errors() : array;
}
