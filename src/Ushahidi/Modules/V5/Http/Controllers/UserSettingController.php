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
    public function index(Request $request)
    {
        $this->authorizeForCurrentUserForUserSetting('index', UserSetting::class);
        $resourceCollection = new UserSettingCollection(
            $this->queryBus->handle(
                new FetchUserSettingQuery(
                    $request->query('limit', FetchUserSettingQuery::DEFAULT_LIMIT),
                    $request->query('page', 1),
                    $request->query('sortBy', "id"),
                    $request->query('order', FetchUserSettingQuery::DEFAULT_ORDER),
                    $this->getSearchData($request->input(), FetchUserSettingQuery::AVAILABLE_SEARCH_FIELDS)
                )
            )
        );
        return $resourceCollection;
    } //end index()


    /**
     * Create new UserSetting.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    //public function store(StoreUserSettingRequest $request, CommandBus $commandBus, QueryBus $queryBus)
    public function store(Request $request)
    {
        $this->authorizeForCurrentUserForUserSetting('store', UserSetting::class);
        $command = new CreateUserSettingCommand($this->getFields($request->input()));
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
    public function update(Request $request, int $id)
    {
        $user_setting = $this->queryBus->handle(new FetchUserSettingByIdQuery($id));
        $this->authorizeForCurrentUserForUserSetting('update', $user_setting);
        $inputs =  $this->getFields($request->input());
        unset($inputs["protected"]);
        $this->commandBus->handle(new UpdateUserSettingCommand($id, $inputs));
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
    public function delete(int $id)
    {
        $user_setting = $this->queryBus->handle(new FetchUserSettingByIdQuery($id));
        $this->authorizeForCurrentUserForUserSetting('delete', $user_setting);

        if ($user_setting->protected) {
            throw new AuthorizationException("Can't delete protected user_setting ");
        }
        $this->commandBus->handle(new DeleteUserSettingCommand($id));
        return new UserSettingResource($user_setting);
    } //end store()


    private function getSearchData(array $input, array $available_search_fields): array
    {
        $search_data = [];
        foreach ($available_search_fields as $field) {
            $search_data[$field] = isset($input[$field]) ? $input[$field] : false;
        }
        return $search_data;
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
