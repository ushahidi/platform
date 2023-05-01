<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Http\Resources\Config\ConfigCollection;
use Ushahidi\Modules\V5\Http\Resources\Config\ConfigResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Config;
use Ushahidi\Modules\V5\Actions\Config\Queries\FindConfigByNameQuery;
use Ushahidi\Modules\V5\Actions\Config\Queries\ListConfigsQuery;

class ConfigController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(string $group_name)
    {
        $group_configs = $this->queryBus->handle(new FindConfigByNameQuery($group_name));
        //$this->authorizeForCurrentUser('show', $group_configs);
        return new ConfigResource($group_configs);
    }//end show()


    /**
     * Display the specified resource.
     *
     * @return TosCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        //$this->authorizeForCurrentUser('index', Config::class);

        return new ConfigCollection($this->queryBus->handle(new ListConfigsQuery()));
    }//end index()

    public function update(Request $request, string $config_name)
    {
    } //end store()
}//end class
