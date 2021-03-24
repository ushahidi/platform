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

class BaseResource extends Resource
{
    public static $wrap = 'result';

    public function getHydrate($relationships, Request $request)
    {
        $only_original = self::toHydrate($request, $relationships);
        return array_filter($only_original, function ($o) use ($relationships) {
            return in_array($o, $relationships);
        });
    }

    public static function toHydrate(Request $request, $relationships)
    {
        $to_hydrate = $relationships;
        if ($request->has('hydrate') && !$request->get('hydrate')) {
            $to_hydrate = [];
        }
        if ($request->get('hydrate')) {
            $to_hydrate = explode(',', $request->get('hydrate'));
        }
        return $to_hydrate;
    }

    public static function onlyOriginal($request, $approved_fields)
    {
        $only_original = $approved_fields;
        if ($request->query('format') === 'minimal') {
            $only_original = ['id', 'name', 'description', 'translations'];
        } elseif ($request->get('only')) {
            $only_original = explode(',', $request->get('only'));
        }
        return $only_original;
    }
    public static function includeFields($request, $approved_fields = [])
    {
        $fields = $approved_fields;
        if ($request->has('only') && !$request->get('only')) {
            return [];
        }
        $only_original = self::onlyOriginal($request, $approved_fields);
        if (count($only_original) > 0) {
            $fields = array_filter($only_original, function ($f) use ($approved_fields) {
                return in_array($f, $approved_fields);
            });
        }
        return $fields;
    }

    protected function setResourceFields($fields)
    {
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $this->$field;
        }
        return $result;
    }
}
