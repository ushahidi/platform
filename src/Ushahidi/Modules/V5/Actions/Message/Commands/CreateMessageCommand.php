<?php

namespace Ushahidi\Modules\V5\Actions\Message\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Message;
use Ushahidi\Modules\V5\Requests\MessageRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Ohanzee\Entities\Message as MessageEntity;
use Ushahidi\Modules\V5\Models\Stage;

class CreateMessageCommand implements Command
{
    /**
     * @var MessageEntity
     */
    private $message_entity;




    public function __construct(MessageEntity $message_entity)
    {
        $this->message_entity = $message_entity;
    }

    public static function fromRequest(MessageRequest $request): self
    {

        $user = Auth::user();
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['parent_id'] = $request->input('parent_id');
        $input['contact_id'] = $request->input('contact_id');
        $input['post_id'] = $request->input('post_id');
        $input['title'] = $request->input('title');
        $input['message'] = $request->input('message');
        $input['datetime'] = $request->input('datetime');
        $input['type'] = $request->input('type');
        $input['status'] = MessageEntity::PENDING; //$request->input('status');
        $input['direction'] = MessageEntity::OUTGOING;//$request->input('direction');
        $input['data_source'] = $request->input('data_source');
        $input['data_source_message_id'] = $request->input('data_source_message_id');
        $input['additional_data'] = $request->input('additional_data');
        $input['notification_post_id'] = $request->input('notification_post_id');
        $input['created'] = time();

        return new self(new MessageEntity($input));
    }

    /**
     * @return MessageEntity
     */
    public function getMessageEntity(): MessageEntity
    {
        return $this->message_entity;
    }
}
