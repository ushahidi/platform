<?php

/**
 * Ushahidi Role Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\ConfidenceScore;

use Ushahidi\Core\Tool\Validator;

class Update extends Validator
{
    protected $default_error_source = 'confidence_score';

    protected function getRules()
    {
        return [
            'id' => [
                ['numeric'],
            ],
            'score' => [
                ['numeric'],
                ['not_empty'],
            ],
            'post_tag_id' => [
                ['numeric'],
                ['not_empty'],
            ],
            'source' => [
                ['not_empty']
            ]
        ];
    }
}
