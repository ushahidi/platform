<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Requests\PasswordresetConfirmRequest;
use Ushahidi\Core\Tool\Hasher\Password as PasswordHash;

class PasswordResetConfirmCommand implements Command
{

    /**
     * @var string
     */
    private $new_password;
    /**
     * @var string
     */
    private $token;

    public function __construct(
        string $new_password,
        string $token
    ) {
        $this->new_password = $new_password;
        $this->token = $token;
    }

    public static function fromRequest(PasswordresetConfirmRequest $request): self
    {
        $new_password = (new PasswordHash())->hash($request->input('password'));
        $token = $request->input('token');
        return new self($new_password, $token);
    }

    public function getNewPassword(): string
    {
        return $this->new_password;
    }
    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
