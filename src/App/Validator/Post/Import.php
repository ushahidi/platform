<?php

/**
 * Ushahidi Post Create Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Post;

class Import extends Create
{
    protected function getRules()
    {
        // We remove the rules validating required stages
        // as stages are not validated during an import
        return array_merge(parent::getRules(), [
        'values' => [
                [[$this, 'checkValues'], [':validation', ':value', ':fulldata']]
          ],
        'completed_stages' => [
                [[$this, 'checkStageInForm'], [':validation', ':value', ':fulldata']]
        ]
        ]);
    }
}
