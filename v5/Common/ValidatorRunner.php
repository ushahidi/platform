<?php
/**
 * *
 *  * Ushahidi Acl
 *  *
 *  * @author     Ushahidi Team <team@ushahidi.com>
 *  * @package    Ushahidi\Application
 *  * @copyright  2020 Ushahidi
 *  * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 *
 */

namespace v5\Common;

use Illuminate\Support\Facades\Validator;

class ValidatorRunner
{

    public static function runValidation($data, $rules, $messages)
    {
        $v = Validator::make($data, $rules, $messages);
        // check for failure
        if (!$v->fails()) {
            return new ValidationResponse(true, null);
        }
        // set errors and return false
        return new ValidationResponse(false, $v->errors());
    }
}
