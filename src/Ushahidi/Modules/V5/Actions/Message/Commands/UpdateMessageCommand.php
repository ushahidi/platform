<?php

namespace Ushahidi\Modules\V5\Actions\Message\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Message;
use Ushahidi\Modules\V5\Requests\MessageRequest;
use Ushahidi\Core\Ohanzee\Entities\Message as MessageEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateMessageCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var MessageEntity
     */
    private $message_entity;

    public function __construct(
        int $id,
        MessageEntity $message_entity
    ) {
        $this->id = $id;
        $this->message_entity = $message_entity;
    }

    public static function fromRequest(int $id, MessageRequest $request, Message $current_message): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->input('user_id') ?? $current_message->user_id;
        } else {
            $input['user_id'] = $current_message->user_id;
        }
        $input['parent_id'] = $request->input('parent_id') ?? $current_message->parent_id;
        $input['contact_id'] = $request->input('contact_id') ?? $current_message->contact_id;
        $input['post_id'] = $request->input('post_id') ?? $current_message->post_id;
        $input['title'] = $request->input('title') ?? $current_message->title;
        $input['message'] = $request->input('message') ?? $current_message->message;
        $input['datetime'] = $request->input('datetime') ?? $current_message->datetime;
        $input['type'] = $request->input('type') ?? $current_message->type;
        $input['status'] = $request->input('status') ?? $current_message->status;
        $input['direction'] = $request->input('direction') ?? $current_message->direction;
        $input['data_source'] = $request->input('data_source') ?? $current_message->data_source;
        $input['data_source_message_id'] = $request->input('data_source_message_id')
            ?? $current_message->data_source_message_id;
        $input['additional_data'] = $request->input('additional_data') ?? $current_message->additional_data;
        $input['notification_post_id'] = $request->input('notification_post_id')
            ?? $current_message->notification_post_id;
        $input['created'] = strtotime($current_message->created);

        return new self($id, new MessageEntity($input));
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
     * @return MessageEntity
     */
    public function getMessageEntity(): MessageEntity
    {
        return $this->message_entity;
    }
}
