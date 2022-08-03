<?php

/**
 * Ushahidi Export Validator
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Validator\ExportJob;

class Create extends Update
{
    protected function getRules()
    {
        return array_merge_recursive(parent::getRules(), [
        'entity_type' => [
        ['not_empty'],
        ],
        ]);
    }
}
