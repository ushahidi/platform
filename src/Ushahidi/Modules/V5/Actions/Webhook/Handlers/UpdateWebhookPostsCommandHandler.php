<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Webhook\Webhook;
use Ushahidi\Modules\V5\Repository\Webhook\WebhookRepository;
use Ushahidi\Modules\V5\Actions\Webhook\Commands\UpdateWebhookCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\WebhookLock as Lock;

class UpdateWebhookPostsCommandHandler extends AbstractCommandHandler
{
    private $webhook_repository;

    public function __construct(WebhookRepository $webhook_repository)
    {
        $this->webhook_repository = $webhook_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateWebhookCommand) {
            throw new \Exception('Provided $command is not instance of UpdateWebhookCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateWebhookCommand $action
         */
        $this->isSupported($action);

        return $this->webhook_repository->update($action->getId(), $action->getWebhookEntity());
    }
}
