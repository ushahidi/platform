<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\DataSource\Contracts\DataSource;
use Ushahidi\Modules\V5\Http\Resources\DataSourceResource;
use Ushahidi\Modules\V5\Http\Resources\DataSourceCollection;

class DataSourceController extends Controller
{
    /**
     * @var \Ushahidi\DataSource\DataSourceManager
     */
    protected $datasources;

    public function __construct()
    {
        // TODO: Middleware to check if datasource is enabled as a feature

        $this->datasources = resolve('datasources');
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        // TODO: Check the Authorizer/Policy if authenticated user has permission to view loaded datasources
        // $this->authorizer->isAllowed(new (Entity::class), 'search');

        /**
         * @var \Illuminate\Support\Collection<\Ushahidi\DataSource\Contracts\DataSource>
         */
        $sources = collect($this->datasources->getSources())
            // Grab the actual source instances
            ->map(function ($name) {
                return $this->datasources->getSource($name);
            })
            // Only include user configurable
            ->filter(function (DataSource $source) {
                return $source->isUserConfigurable();
            });

        $searchParams = new SearchData($request->input());
        // Filter by type
        if ($searchParams->type) {
            $sources = $sources->filter(function (DataSource $source) use ($searchParams) {
                return in_array($searchParams->type, $source->getServices());
            });
        }

        $resource = (new DataSourceCollection(
            $sources->flatten()
        ))->additional([
            'message' => 'Fetched configurable datasources',
            'count' => $sources->count()
        ]);

        return $resource;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $source
     */
    public function show($source)
    {
        try {
            return new DataSourceResource(
                $this->datasources->getSource($source)
            );
        } catch (\InvalidArgumentException $e) {
            return response()->isNotFound();
        }
    }
}
