<?php

namespace Ushahidi\Modules\V5\Actions\User\Commands;

use App\Bus\Command\Command;

class DeleteUserSettingCommand implements Command
{


    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $user_id;

    public function __construct(int $id, int $user_id)
    {
        $this->id = $id;
        $this->user_id = $user_id;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }
}
