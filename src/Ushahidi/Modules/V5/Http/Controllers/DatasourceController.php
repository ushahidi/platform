<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Datasource\Queries\FetchDataSourceQuery;
use Ushahidi\Modules\V5\Actions\Datasource\Queries\SearchDataSourcesQuery;
use Ushahidi\Modules\V5\Http\Resources\Datasource\DataSourceCollection;
use Ushahidi\Modules\V5\Http\Resources\Datasource\DataSourceResource;

class DatasourceController extends Controller
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function index(Request $request): DataSourceCollection
    {
        $query = new SearchDataSourcesQuery($request->input('type'));

        return new DataSourceCollection($this->queryBus->handle($query));
    }

    public function show(string $source): DataSourceResource
    {
        $query = new FetchDataSourceQuery($source);

        return new DataSourceResource($this->queryBus->handle($query));
    }
}
