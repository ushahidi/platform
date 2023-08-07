<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Requests\WebhookRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\Webhook as WebhookEntity;

class CreateWebhookCommand implements Command
{
    /**
     * @var WebhookEntity
     */
    private $webhook_entity;


    

    public function __construct(WebhookEntity $webhook_entity)
    {
        $this->webhook_entity = $webhook_entity;
    }

    public static function fromRequest(WebhookRequest $request): self
    {

        $user = Auth::user();
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['form_id'] = $request->input('form_id');
        $input['name'] = $request->input('name');
        $input['shared_secret'] = $request->input('shared_secret');
        $input['webhook_uuid'] = $request->input('webhook_uuid');
        $input['url'] = $request->input('url');
        $input['event_type'] = $request->input('event_type');
        $input['entity_type'] = $request->input('entity_type');
        $input['source_field_key'] = $request->input('source_field_key');
        $input['destination_field_key'] = $request->input('destination_field_key');

        $input['created'] = time();
        $input['updated'] = null;

        return new self(new WebhookEntity($input));
    }

    /**
     * @return WebhookEntity
     */
    public function getWebhookEntity(): WebhookEntity
    {
        return $this->webhook_entity;
    }
}
