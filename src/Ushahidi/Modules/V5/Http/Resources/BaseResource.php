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

namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Modules\V5\Traits\HasHydrate;
use Ushahidi\Modules\V5\Traits\HasOnlyParameters;

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



    /**
     * get the approved only fields from the request
     * @param Request $request
     * @param array $approved_fields
     * @param array $required_fields
     * @return array
     */
    public function onlyFields(Request $request, array $approved_fields = [], array $required_fields = [])
    {
        $only_fields = $approved_fields;
        if ($request->has('only') && !$request->get('only')) {
            return [];
        }
        if ($request->query('format') === 'minimal') {
            $only_fields = ['id', 'name', 'description', 'translations'];
        } elseif ($request->get('only')) {
            $only_fields = explode(',', $request->get('only'));
        }

        if (count($only_fields) > 0) {
            $only_fields = array_filter($only_fields, function ($f) use ($approved_fields) {
                return in_array($f, $approved_fields);
            });
        }
        return array_merge($required_fields, $only_fields);
    }


    /**
     * get the approved hydrate relations from the request
     * @param Request $request
     * @param array $approved_relations
     * @param array $required_relations
     * @return array
     */
    public function hydrateRelations(Request $request, array $approved_relations = [], array $required_relations = [])
    {
        $hydrate_relations = $approved_relations;
        if ($request->has('hydrate') && !$request->get('hydrate')) {
            return [];
        }
        if ($request->get('hydrate')) {
            $hydrate_relations = explode(',', $request->get('hydrate'));
        }
        if (count($hydrate_relations) > 0) {
            $hydrate = array_filter($hydrate_relations, function ($f) use ($approved_relations) {
                return in_array($f, $approved_relations);
            });
        }
        return array_merge($required_relations, $hydrate);
    }
}
