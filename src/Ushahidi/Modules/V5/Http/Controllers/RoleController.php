<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use App\Bus\Command\CommandBus;
use Ushahidi\Modules\V5\Http\Resources\Role\RoleCollection;
use Ushahidi\Modules\V5\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Role;
use Ushahidi\Modules\V5\Actions\Role\Queries\FetchRoleByIdQuery;
use Ushahidi\Modules\V5\Actions\Role\Queries\FetchRoleQuery;
use Ushahidi\Modules\V5\Requests\StoreRoleRequest;
use Ushahidi\Modules\V5\Actions\Role\Commands\CreateRoleCommand;
use Ushahidi\Modules\V5\Actions\Role\Commands\DeleteRoleCommand;
use Ushahidi\Modules\V5\Actions\Role\Commands\UpdateRoleCommand;
use Ushahidi\Core\Exception\AuthorizerException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Access\Gate;

class RoleController extends V5Controller
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
        $role = $queryBus->handle(new FetchRoleByIdQuery($id));
        $this->authorizeForCurrentUserForRole('show', $role);
        return new RoleResource($role);
    }//end show()

   
    /**
     * Display the specified resource.
     *
     * @return RoleCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, QueryBus $queryBus)
    {
        $this->authorizeForCurrentUserForRole('index', Role::class);
        $resourceCollection = new RoleCollection(
            $queryBus->handle(
                new FetchRoleQuery(
                    $request->query('limit', FetchRoleQuery::DEFAULT_LIMIT),
                    $request->query('page', 1),
                    $request->query('sortBy', "id"),
                    $request->query('order', FetchRoleQuery::DEFAULT_ORDER),
                    $this->getSearchData($request->input(), FetchRoleQuery::AVAILABLE_SEARCH_FIELDS)
                )
            )
        );
         return $resourceCollection;
    }//end index()

    
    /**
     * Create new Role.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    //public function store(StoreRoleRequest $request, CommandBus $commandBus, QueryBus $queryBus)
    public function store(Request $request, CommandBus $commandBus, QueryBus $queryBus)
    {
        $this->authorizeForCurrentUserForRole('store', Role::class);
        $command = new CreateRoleCommand($this->getFields($request->input()));
         $commandBus->handle($command);
         return new RoleResource(
             $queryBus->handle(new FetchRoleByIdQuery($command->getId()))
         );
    }//end store()

    
    /**
     * update Role.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|RoleResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id, CommandBus $commandBus, QueryBus $queryBus)
    {
        $role = $queryBus->handle(new FetchRoleByIdQuery($id));
        $this->authorizeForCurrentUserForRole('update', $role);
        $command = new UpdateRoleCommand(
            $id,
            $this->getFields($request->input()),
        );
         $commandBus->handle($command);
         return new RoleResource(
             $queryBus->handle(new FetchRoleByIdQuery($command->getId()))
         );
    }//end store()


     /**
     * delete Role.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|RoleResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id, CommandBus $commandBus, QueryBus $queryBus)
    {
        $role = $queryBus->handle(new FetchRoleByIdQuery($id));
        $this->authorizeForCurrentUserForRole('delete', $role);

        if ($role->protected) {
            throw new AuthorizationException("Can't delete protected role ");
        }
         $commandBus->handle(new DeleteRoleCommand($id));
         return new RoleResource($role);
    }//end store()


    private function getSearchData(array $input, array $available_search_fields): array
    {
        $search_data = [];
        foreach ($available_search_fields as $field) {
            $search_data[$field] = isset($input[$field]) ? $input[$field] : false;
        }
        return $search_data;
    }

    // To Do : Replace with authorizeForCurrentUser after merge
    public function authorizeForCurrentUserForRole($ability, $arguments = [])
    {
        $gUser = $this->getGenericUserForRole();

        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);
        return app(Gate::class)->forUser($gUser)->authorize($ability, $arguments);
    }

    public function getGenericUserForRole()
    {
        return  Auth::guard()->user();
    }
}//end class
