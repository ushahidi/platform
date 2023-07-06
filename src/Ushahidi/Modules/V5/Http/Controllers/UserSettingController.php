<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Ushahidi\Modules\V5\Http\Resources\User\UserSettingCollection;
use Ushahidi\Modules\V5\Http\Resources\User\UserSettingResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\UserSetting;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserSettingByIdQuery;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserSettingQuery;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserByIdQuery;
use Ushahidi\Modules\V5\Actions\User\Commands\CreateUserSettingCommand;
use Ushahidi\Modules\V5\Actions\User\Commands\DeleteUserSettingCommand;
use Ushahidi\Modules\V5\Actions\User\Commands\UpdateUserSettingCommand;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\UserSetting as UserSettingEntity;
use Ushahidi\Modules\V5\Requests\UserSettingRequest;

class UserSettingController extends V5Controller
{
    private function checkUser(int $user_id)
    {
         $this->queryBus->handle(new FetchUserByIdQuery($user_id));
    }

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $user_id, int $id)
    {
        $user_setting = $this->queryBus->handle(new FetchUserSettingByIdQuery($id));
        $this->checkUser($user_id);
        $this->authorize('show', $user_setting);
        return new UserSettingResource($user_setting);
    } //end show()


    /**
     * Display the specified resource.
     *
     * @return UserSettingCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, int $user_id)
    {
        $this->authorize('index', new UserSetting());
        $this->checkUser($user_id);
        return new UserSettingCollection(
            $this->queryBus->handle(
                new FetchUserSettingQuery(
                    $user_id,
                    $request->query('limit', FetchUserSettingQuery::DEFAULT_LIMIT),
                    $request->query('page', 1),
                    $request->query('sortBy', "id"),
                    $request->query('order', FetchUserSettingQuery::DEFAULT_ORDER),
                    []
                    // $this->getSearchData($request->input(), FetchUserSettingQuery::AVAILABLE_SEARCH_FIELDS)
                )
            )
        );
    } //end index()


    /**
     * Create new UserSetting.
     *
     * @param UserSettingRequest $request
     * @return \Illuminate\Http\JsonResponse|UserSettingResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(UserSettingRequest $request, int $user_id)
    {
        $this->authorize('store', new UserSetting());
        $this->checkUser($user_id);
        $command = new CreateUserSettingCommand(
            UserSettingEntity::buildEntity(array_merge($request->input(), ["user_id" => $user_id]))
        );
        $this->commandBus->handle($command);
        return new UserSettingResource(
            $this->queryBus->handle(new FetchUserSettingByIdQuery($command->getId()))
        );
    } //end store()

    /**
     * update UserSetting.
     *
     * @param UserSettingRequest $request
     * @return \Illuminate\Http\JsonResponse|UserSettingResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UserSettingRequest $request, int $user_id, int $id)
    {
        $user_setting = $this->queryBus->handle(new FetchUserSettingByIdQuery($id));
        $this->authorize('update', $user_setting);
        $this->checkUser($user_id);
        $this->commandBus->handle(
            new UpdateUserSettingCommand(
                $id,
                UserSettingEntity::buildEntity(
                    array_merge($request->input(), ["user_id" => $user_id]),
                    'update',
                    $user_setting->toArray()
                )
            )
        );
        return new UserSettingResource(
            $this->queryBus->handle(new FetchUserSettingByIdQuery($id))
        );
    } //end store()


    /**
     * delete UserSetting.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|UserSettingResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $user_id, int $id)
    {
        $user_setting = $this->queryBus->handle(new FetchUserSettingByIdQuery($id));
        $this->authorize('delete', $user_setting);
        $this->checkUser($user_id);
        $this->commandBus->handle(new DeleteUserSettingCommand($id, $user_id));
        return $this->deleteResponse($id);
    } //end store()
} //end class
