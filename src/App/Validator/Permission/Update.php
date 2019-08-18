<?php

/**
 * Ushahidi Permission Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Permission;

use Ushahidi\App\Validator\LegacyValidator;

class Update extends LegacyValidator
{
    protected $default_error_source = 'permission';

    protected function getRules()
    {
        return [
            'id' => [
                ['numeric'],
            ],
        ];
    }
}
