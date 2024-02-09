<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Ushahidi\Modules\V5\Http\Resources\Role\RoleCollection;
use Ushahidi\Modules\V5\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Role;
use Ushahidi\Modules\V5\Actions\Role\Queries\FetchRoleByIdQuery;
use Ushahidi\Modules\V5\Actions\Role\Queries\FetchRoleQuery;
use Ushahidi\Modules\V5\Actions\Role\Commands\CreateRoleCommand;
use Ushahidi\Modules\V5\Actions\Role\Commands\DeleteRoleCommand;
use Ushahidi\Modules\V5\Actions\Role\Commands\UpdateRoleCommand;
use Ushahidi\Core\Entity\Role as RoleEntity;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Requests\RoleRequest;
use Ushahidi\Modules\V5\DTO\RoleSearchFields;

class RoleController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $role = $this->queryBus->handle(new FetchRoleByIdQuery($id));
        $this->authorize('show', $role);
        return new RoleResource($role);
    } //end show()


    /**
     * Display the specified resource.
     *
     * @return RoleCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', new ROLE());

        $resourceCollection = new RoleCollection(
            $this->queryBus->handle(
                new FetchRoleQuery(
                    $request->query('limit', FetchRoleQuery::DEFAULT_LIMIT),
                    $request->query('page', 1),
                    $request->query('sortBy', FetchRoleQuery::DEFAULT_SORT_BY),
                    $request->query('order', FetchRoleQuery::DEFAULT_ORDER),
                    new RoleSearchFields($request)
                )
            )
        );
        return $resourceCollection;
    } //end index()


    /**
     * Create new Role.
     *
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse|RoleResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(RoleRequest $request)
    {
        $this->authorize('store', new ROLE());

        $command = new CreateRoleCommand(
            RoleEntity::buildEntity($request->input()),
            $request->input('permissions') ?? []
        );
        $this->commandBus->handle($command);
        return new RoleResource(
            $this->queryBus->handle(new FetchRoleByIdQuery($command->getId()))
        );
    } //end store()

    /**
     * update Role.
     *
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse|RoleResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(RoleRequest $request, int $id)
    {
        $role = $this->queryBus->handle(new FetchRoleByIdQuery($id));
        $this->authorize('update', $role);

        if ($role->name !== $request->input('name')) {
            return self::make422("Role name cannot be updated.");
        }

        $permissions = [];
        if ($role->name === RoleEntity::ADMIN) {
            foreach($role->getPermission()->toArray() as $permission) {
                $permissions[] = $permission['permission'];
            }
        }
        else {
            $permissions = $request->input('permissions') ?? [];
        }

        $this->commandBus->handle(
            new UpdateRoleCommand(
                $id,
                RoleEntity::buildEntity($request->input(), 'update', $role->toArray()),
                $permissions
            )
        );

        return new RoleResource(
            $this->queryBus->handle(new FetchRoleByIdQuery($id))
        );
    } //end store()


    /**
     * delete Role.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|RoleResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        $role = $this->queryBus->handle(new FetchRoleByIdQuery($id));
        $this->authorize('delete', $role);
        if ($role->protected) {
            throw new AuthorizationException("Can't delete protected role ");
        }
        $this->commandBus->handle(new DeleteRoleCommand($id));
        return $this->deleteResponse($id);
    } //end store()
} //end class
