<?php

namespace Ushahidi\Modules\V5\Actions\User\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\UserSetting as UserSettingEntity;

class CreateUserSettingCommand implements Command
{
    /**
     * @var UserSettingEntity
     */
    private $user_setting_entity;

    /**
     * @var int
     */
    private $id;

    public function __construct(UserSettingEntity $user_setting_entity)
    {
        $this->user_setting_entity = $user_setting_entity;
    }

    /**
     * @return UserSettingEntity
     */
    public function getEntity(): UserSettingEntity
    {
        return $this->user_setting_entity;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return  $this->id;
    }

    /**
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
