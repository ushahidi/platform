<?php

/**
 * Ushahidi Webhook Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Webhook;

class Create extends Update
{
    protected function getRules()
    {
        return array_merge_recursive(parent::getRules(), [
        'name' => [
        ['not_empty'],
        ],
        'shared_secret' => [
        ['not_empty'],
        ],
        'url' => [
        ['not_empty'],
        ],
        'event_type' => [
        ['not_empty'],
        ],
        'entity_type' => [
        ['not_empty'],
        ],
        ]);
    }
}
