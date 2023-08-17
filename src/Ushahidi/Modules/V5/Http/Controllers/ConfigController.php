<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Http\Resources\Config\ConfigCollection;
use Ushahidi\Modules\V5\Http\Resources\Config\ConfigResource;
use Ushahidi\Modules\V5\Http\Resources\Config\ConfigKeyResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Config;
use Ushahidi\Modules\V5\Actions\Config\Queries\FindConfigByNameQuery;
use Ushahidi\Modules\V5\Actions\Config\Queries\ListConfigsQuery;
use Ushahidi\Modules\V5\Actions\Config\Commands\UpdateConfigCommand;
use Ushahidi\Modules\V5\DTO\ConfigSearchFields;

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
        $this->authorizeAnyone('show', new Config(["group_name"=>$group_name]));
        return new ConfigResource($group_configs);
    } //end show()

    public function showKey(string $group_name, string $key)
    {

        $group_configs = $this->queryBus->handle(new FindConfigByNameQuery($group_name, $key));
        $this->authorizeAnyone('show', new Config(["group_name"=>$group_name]));
        return new ConfigKeyResource($group_configs);
    } //end show()


    /**
     * Display the specified resource.
     *
     * @return TosCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorizeAnyone('index', Config::class);
        return new ConfigCollection($this->queryBus->handle(new ListConfigsQuery(new ConfigSearchFields($request))));
    } //end index()

    public function update(Request $request, string $group_name)
    {
        $current_group_configs = $this->queryBus->handle(new FindConfigByNameQuery($group_name));
        $this->authorize('update', new Config(["group_name"=>$group_name]));
            $this->commandBus->handle(UpdateConfigCommand::fromRequest(
                $group_name,
                $request,
                $current_group_configs->toArray()
            ));
        return $this->show($group_name);
    }
 
    public function updateKey(Request $request, string $group_name, string $key)
    {
        $current_group_configs = $this->queryBus->handle(new FindConfigByNameQuery($group_name));
        $this->authorize('update', new Config(["group_name"=>$group_name]));
        $this->commandBus->handle(UpdateConfigCommand::fromRequest(
            $group_name,
            $request,
            $current_group_configs->toArray(),
            $key
        ));
        return $this->showKey($group_name, $key);
    }
} //end class
