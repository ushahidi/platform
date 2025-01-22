<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Http\Resources\Permissions\PermissionsCollection;
use Ushahidi\Modules\V5\Http\Resources\Permissions\PermissionsResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Permissions;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsByIdQuery;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsQuery;

class PermissionsController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id, QueryBus $queryBus)
    {
        $permissions = $queryBus->handle(new FetchPermissionsByIdQuery($id));
        $this->authorizeForCurrentUser('show', $permissions);
        return new PermissionsResource($permissions);
    }//end show()


    /**
     * Display the specified resource.
     *
     * @return TosCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, QueryBus $queryBus)
    {
        $this->authorizeForCurrentUser('index', Permissions::class);
        $resourceCollection = new PermissionsCollection(
            $queryBus->handle(
                new FetchPermissionsQuery(
                    $request->query('limit', FetchPermissionsQuery::DEFAULT_LIMIT),
                    $request->query('page', 1),
                    $request->query('sortBy', FetchPermissionsQuery::DEFAULT_SORT_BY),
                    $request->query('order', FetchPermissionsQuery::DEFAULT_ORDER),
                    $this->getSearchData($request->input(), FetchPermissionsQuery::AVAILABLE_SEARCH_FIELDS)
                )
            )
        );
         return $resourceCollection;
    }//end index()

    private function getSearchData(array $input, array $available_search_fields): array
    {
        $search_data = [];
        foreach ($available_search_fields as $field) {
            $search_data[$field] = isset($input[$field]) ? $input[$field] : false;
        }
        return $search_data;
    }
}//end class
