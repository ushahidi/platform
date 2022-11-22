<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use App\Bus\Command\CommandBus;
use Ushahidi\Modules\V5\Http\Resources\User\UserSettingCollection;
use Ushahidi\Modules\V5\Http\Resources\User\UserSettingResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\UserSetting;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserSettingByIdQuery;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserSettingQuery;
use Ushahidi\Modules\V5\Requests\StoreUserSettingRequest;
use Ushahidi\Modules\V5\Actions\User\Commands\CreateUserSettingCommand;
use Ushahidi\Modules\V5\Actions\User\Commands\DeleteUserSettingCommand;
use Ushahidi\Modules\V5\Actions\User\Commands\UpdateUserSettingCommand;
use Ushahidi\Core\Exception\AuthorizerException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Access\Gate;
use Ushahidi\Core\Entity\UserSetting as UserSettingEntity;

class UserSettingController extends V5Controller
{

    private $queryBus;
    private $commandBus;
    public function __construct(QueryBus $queryBus, CommandBus $commandBus)
    {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $user_setting = $this->queryBus->handle(new FetchUserSettingByIdQuery($id));
        $this->authorizeForCurrentUserForUserSetting('show', $user_setting);
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
        $this->authorizeForCurrentUserForUserSetting('index', UserSetting::class);
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    //public function store(StoreUserSettingRequest $request, CommandBus $commandBus, QueryBus $queryBus)
    public function store(Request $request, int $user_id)
    {
        $this->authorizeForCurrentUserForUserSetting('store', UserSetting::class);
        $command = new CreateUserSettingCommand($this->buildEntity("create", $user_id, $request));
        $this->commandBus->handle($command);
        return new UserSettingResource(
            $this->queryBus->handle(new FetchUserSettingByIdQuery($command->getId()))
        );
    } //end store()

    /**
     * update UserSetting.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|UserSettingResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $user_id, int $id)
    {
        $user_setting = $this->queryBus->handle(new FetchUserSettingByIdQuery($id));
        $this->authorizeForCurrentUserForUserSetting('update', $user_setting);
        $this->commandBus->handle(
            new UpdateUserSettingCommand(
                $id,
                $this->buildEntity("update", $user_id, $request, $user_setting)
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
        $this->authorizeForCurrentUserForUserSetting('delete', $user_setting);
        $this->commandBus->handle(new DeleteUserSettingCommand($id, $user_id));
        return new UserSettingResource($user_setting);
    } //end store()

    private function buildEntity(
        string $action,
        int $user_id,
        Request $request,
        UserSetting $user_setting = null
    ): UserSettingEntity {
        if ($action === "update") {
            $user_entity = new UserSettingEntity([
                "id" => $user_setting->id,
                "config_key" => $request->input("config_key", $user_setting->email),
                "config_value" => $request->input("config_value", $user_setting->email),
                "user_id" => $user_id,
                "created" => $user_setting->created,
                "updated" => time()
            ]);
        } else { // create
            $user_entity = new UserSettingEntity([
                "config_key" => $request->input("config_key"),
                "config_value" => $request->input("config_value"),
                "user_id" => $user_id,
                "created" => time(),
                "updated" => time()

            ]);
        }
        return ($user_entity);
    }

    // To Do : Replace with authorizeForCurrentUser after merge
    private function authorizeForCurrentUserForUserSetting($ability, $arguments = [])
    {
        $gUser = $this->getGenericUserForUserSetting();

        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);
        return app(Gate::class)->forUser($gUser)->authorize($ability, $arguments);
    }

    private function getGenericUserForUserSetting()
    {
        return  Auth::guard()->user();
    }
}//end class
