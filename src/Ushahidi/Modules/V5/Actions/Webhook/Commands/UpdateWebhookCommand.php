<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Webhook\Webhook;
use Ushahidi\Modules\V5\Requests\WebhookRequest;
use Ushahidi\Core\Entity\Webhook as WebhookEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateWebhookCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var WebhookEntity
     */
    private $webhook_entity;

    public function __construct(
        int $id,
        WebhookEntity $webhook_entity
    ) {
        $this->id = $id;
        $this->webhook_entity = $webhook_entity;
    }

    public static function fromRequest(int $id, WebhookRequest $request, Webhook $current_webhook): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->input('user_id') ?? $current_webhook->user_id;
        } else {
            $input['user_id'] = $current_webhook->user_id;
        }

        $input['form_id'] = $request->input('form_id') ?? $current_webhook->form_id;
        $input['name'] = $request->input('name')??$current_webhook->name;
        $input['shared_secret'] = $request->input('shared_secret') ?? $current_webhook->shared_secret;
        $input['webhook_uuid'] = $request->input('webhook_uuid') ?? $current_webhook->webhook_uuid;
        $input['url'] = $request->input('url') ?? $current_webhook->url;
        $input['event_type'] = $request->input('event_type') ?? $current_webhook->event_type;
        $input['entity_type'] = $request->input('entity_type') ?? $current_webhook->entity_type;
        $input['source_field_key'] = $request->input('source_field_key') ?? $current_webhook->source_field_key;
        $input['destination_field_key'] = $request->input('destination_field_key') ?? $current_webhook->destination_field_key;
        $input['created'] = strtotime($current_webhook->created);
        $input['updated'] = time();

        return new self($id, new WebhookEntity($input));
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
     * @return WebhookEntity
     */
    public function getWebhookEntity(): WebhookEntity
    {
        return $this->webhook_entity;
    }
}
