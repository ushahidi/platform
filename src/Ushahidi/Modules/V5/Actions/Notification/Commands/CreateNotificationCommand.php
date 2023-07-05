<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Notification;
use Ushahidi\Modules\V5\Requests\NotificationRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Ohanzee\Entities\Notification as NotificationEntity;
use Ushahidi\Modules\V5\Models\Stage;

class CreateNotificationCommand implements Command
{
    /**
     * @var NotificationEntity
     */
    private $notification_entity;




    public function __construct(NotificationEntity $notification_entity)
    {
        //dd($notification_entity);
        $this->notification_entity = $notification_entity;
    }

    public static function fromRequest(NotificationRequest $request): self
    {

        $user = Auth::user();
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['set_id'] = $request->input('set_id');
        $input['created'] = time();

        return new self(new NotificationEntity($input));
    }

    /**
     * @return NotificationEntity
     */
    public function getNotificationEntity(): NotificationEntity
    {
        return $this->notification_entity;
    }
}
