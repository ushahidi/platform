<?php

/**
 * Ushahidi Permission Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Permission;

class Create extends Update
{
    protected function getRules()
    {
        return parent::getRules() +
            [
                'name' => [
                    ['not_empty'],
                ]
            ];
    }
}
