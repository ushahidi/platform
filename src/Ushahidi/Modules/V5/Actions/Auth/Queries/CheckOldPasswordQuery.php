<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Queries;

use App\Bus\Query\Query;
use Illuminate\Http\Request;

class CheckOldPasswordQuery implements Query
{

    private $email;
    private $passwordToCheck;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->passwordToCheck = $password;
    }

    public static function fromRequest(Request $request): self
    {
        $query =  new self($request->get('email'), $request->get('password'));
        return $query;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordToCheck(): string
    {
        return $this->passwordToCheck;
    }
}
