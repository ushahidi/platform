<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Notification;
use Ushahidi\Modules\V5\Requests\NotificationRequest;
use Ushahidi\Core\Entity\Notification as NotificationEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateNotificationCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var NotificationEntity
     */
    private $notification_entity;

    public function __construct(
        int $id,
        NotificationEntity $notification_entity
    ) {
        $this->id = $id;
        $this->notification_entity = $notification_entity;
    }

    public static function fromRequest(int $id, NotificationRequest $request, Notification $current_notification): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->input('user_id') ?? $current_notification->user_id;
        } else {
            $input['user_id'] = $current_notification->user_id;
        }
        $input['set_id'] = $request->input('set_id') ?? $current_notification->set_id;
        $input['created'] = strtotime($current_notification->created);

        return new self($id, new NotificationEntity($input));
    }
    private static function hasPermissionToUpdateUser($user)
    {
        if ($user->role === "admin") {
            return true;
        }
        return false;
    }

    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return NotificationEntity
     */
    public function getNotificationEntity(): NotificationEntity
    {
        return $this->notification_entity;
    }
}
