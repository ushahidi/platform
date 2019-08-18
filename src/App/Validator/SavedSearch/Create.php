<?php

/**
 * Ushahidi Set Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\SavedSearch;

use Ushahidi\App\Validator\Set;

class Create extends Set\Create
{
    protected function getRules()
    {
        $rules = parent::getRules();
        return array_merge_recursive($rules, [
        'name' => [
            ['not_empty'],
        ],
        'filter' => [
                ['not_empty'],
            ]
        ]);
    }
}
