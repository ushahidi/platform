<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Webhook\Webhook;
use Ushahidi\Modules\V5\Requests\WebhookRequest;
use Ushahidi\Core\Entity\Webhook as WebhookEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateWebhookPostsCommand implements Command
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
            $input['user_id'] = $request->has('user_id') ? $request->input('user_id') : $current_webhook->user_id;
        } else {
            $input['user_id'] = $current_webhook->user_id;
        }

        $input['data_source'] = $request->has('data_source')
            ? $request->input('data_source') : $current_webhook->data_source;
        $input['type'] = $request->has('type') ? $request->input('type') : $current_webhook->type;
        $input['webhook'] = $request->has('webhook') ? $request->input('webhook') : $current_webhook->webhook;
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
