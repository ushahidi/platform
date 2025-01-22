<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Requests\ResetPasswordRequest;

class PasswordResetCommand implements Command
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function fromRequest(ResetPasswordRequest $request): self
    {
        $email= $request->input('email');
        return new self($email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
