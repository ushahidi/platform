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

namespace v5\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use v5\Traits\HasHydrate;
use v5\Traits\HasOnlyParameters;

class BaseResource extends Resource
{
    use HasHydrate;
    use HasOnlyParameters;
    
    public static $wrap = 'result';

   
    protected function setResourceFields($fields)
    {
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $this->$field;
        }
        return $result;
    }
}
