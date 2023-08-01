<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Commands;

use App\Bus\Command\Command;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\User as UserEntity;
use Ushahidi\Modules\V5\Requests\RegisterRequest;
use Ushahidi\Core\Tool\Hasher\Password as PasswordHash;

class RegisterCommand implements Command
{
    /**
     * @var UserEntity
     */
    private $user_entity;


    

    public function __construct(UserEntity $user_entity)
    {
        $this->user_entity = $user_entity;
    }

    public static function fromRequest(RegisterRequest $request): self
    {

        $input['email'] = $request->input('email');
        $input['password'] = (new PasswordHash())->hash($request->input('password'));
        $input['realname'] = $request->input('realname');
        $input['logins'] = UserEntity::DEFAULT_LOGINS;
        $input['failed_attempts'] = UserEntity::DEFAULT_FAILED_ATTEMPTS;
        $input['last_login'] = UserEntity::DEFAULT_LAST_LOGIN;
        $input['language'] = UserEntity::DEFAULT_LANGUAGE;

        $input['created'] = time();
        $input['updated'] = null;

        return new self(new UserEntity($input));
    }

    /**
     * @return UserEntity
     */
    public function getUserEntity(): UserEntity
    {
        return $this->user_entity;
    }
}
