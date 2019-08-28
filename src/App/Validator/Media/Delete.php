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

use Ushahidi\Core\Data;
use Ushahidi\App\Validator\LegacyValidator;

class Delete extends LegacyValidator
{
    protected $default_error_source = 'media';

    protected function getRules()
    {
        return [
            'id' => [
                ['not_empty'],
                ['digit'],
            ],
        ];
    }
}
