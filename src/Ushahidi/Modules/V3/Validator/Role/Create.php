<?php

/**
 * Ushahidi Role Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Role;

class Create extends Update
{
    protected function getRules()
    {
        return array_merge_recursive(parent::getRules(), [
                'name' => [
                    ['not_empty'],
                ]
        ]);
    }
}
