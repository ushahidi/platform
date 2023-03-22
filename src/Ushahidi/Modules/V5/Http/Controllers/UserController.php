<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use App\Bus\Query\QueryBus;
use App\Bus\Command\CommandBus;
use Ushahidi\Modules\V5\Http\Resources\User\UserCollection;
use Ushahidi\Modules\V5\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\User;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserByIdQuery;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserQuery;
use Ushahidi\Modules\V5\Actions\User\Commands\CreateUserCommand;
use Ushahidi\Modules\V5\Actions\User\Commands\DeleteUserCommand;
use Ushahidi\Modules\V5\Actions\User\Commands\UpdateUserCommand;
use Ushahidi\Core\Exception\AuthorizerException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Access\Gate;
use Ushahidi\Core\Entity\User as UserEntity;
use Ushahidi\Modules\V5\DTO\UserSearchFields;
//use Ushahidi\Core\Tool\Hasher\Password as PasswordHash;
use Ushahidi\Modules\V5\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use Ushahidi\Core\Exception\NotFoundException;

class UserController extends V5Controller
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
        $user = $this->queryBus->handle(new FetchUserByIdQuery($id));
        $this->authorizeForCurrentUserForUser('show', $user);
        return new UserResource($user);
    } //end show()


    /**
     * Display Me.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showMe()
    {
        $id = $this->getGenericUserForUser()->id;
        if (!$id) {
            throw new NotFoundException('User not found');
        }
        $user = $this->queryBus->handle(new FetchUserByIdQuery($id));
        $this->authorizeForCurrentUserForUser('show', $user);
        return new UserResource($user);
    } //end show()

    /**
     * Display the specified resource.
     *
     * @return UserCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorizeForCurrentUserForUser('index', User::class);
        $resourceCollection = new UserCollection(
            $this->queryBus->handle(
                new FetchUserQuery(
                    $request->query('limit', FetchUserQuery::DEFAULT_LIMIT),
                    $request->query('page', 1),
                    $request->query('sortBy', "id"),
                    $request->query('order', FetchUserQuery::DEFAULT_ORDER),
                    new UserSearchFields($request)
                )
            )
        );
        return $resourceCollection;
    } //end index()


    /**
     * Create new User.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse|CategoryResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(UserRequest $request)
    {
        $this->authorizeForCurrentUserForUser('store', User::class);
        $command = new CreateUserCommand(UserEntity::buildEntity($request->input()));
        $this->commandBus->handle($command);
        return new UserResource(
            $this->queryBus->handle(new FetchUserByIdQuery($command->getId()))
        );
    } //end store()


    /**
     * update User.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse|UserResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UserRequest $request, int $id)
    {
        $user = $this->queryBus->handle(new FetchUserByIdQuery($id));
        $this->authorizeForCurrentUserForUser('update', $user);
        $this->commandBus->handle(
            new UpdateUserCommand($id, UserEntity::buildEntity($request->input(), 'update', $user->toArray()))
        );
        return new UserResource(
            $this->queryBus->handle(new FetchUserByIdQuery($id))
        );
    } //end update()

    /**
     * update Me.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse|UserResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateMe(UserRequest $request)
    {
        $id = AUTH::id();//$this->getGenericUserForUser()->id;
        $user = $this->queryBus->handle(new FetchUserByIdQuery($id));
        $this->authorizeForCurrentUserForUser('update', $user);
        $this->commandBus->handle(
            new UpdateUserCommand($id, $this->buildUserEntity("update", $request, $user))
        );
        return new UserResource(
            $this->queryBus->handle(new FetchUserByIdQuery($id))
        );
    } //end update()


    /**
     * delete User.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|UserResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        $user = $this->queryBus->handle(new FetchUserByIdQuery($id));
        $this->authorizeForCurrentUserForUser('delete', $user);
        $this->commandBus->handle(new DeleteUserCommand($id));
        return $this->deleteResponse($id);
    } //end store()



    // To Do : Replace with authorizeForCurrentUser after merge
    private function authorizeForCurrentUserForUser($ability, $arguments = [])
    {
        $gUser = $this->getGenericUserForUser();

        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);
        return app(Gate::class)->forUser($gUser)->authorize($ability, $arguments);
    }

    private function getGenericUserForUser()
    {
        return Auth::guard()->user();
    }
} //end class
