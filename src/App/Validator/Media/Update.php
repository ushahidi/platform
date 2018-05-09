<?php

/**
 * Ushahidi Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Media;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Update extends Create
{
    protected function getRules()
    {
        return [
            'user_id' => [
                ['digit'],
            ],
            'caption' => [
                // alphas, numbers, punctuation, and spaces
                ['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
            ]
        ];
    }
}
