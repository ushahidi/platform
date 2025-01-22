<?php

/**
 * Ushahidi Post Point Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Post;

class Geometry extends ValueValidator
{
    protected function validate($value)
    {
        if (!is_scalar($value)) {
            return 'scalar';
        }
    }
}
