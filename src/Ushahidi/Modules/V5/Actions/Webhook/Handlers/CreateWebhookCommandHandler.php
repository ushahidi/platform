<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Webhook\Commands\CreateWebhookCommand;
use Ushahidi\Modules\V5\Repository\Webhook\WebhookRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Webhook;

class CreateWebhookCommandHandler extends AbstractCommandHandler
{
    private $webhook_repository;

    public function __construct(WebhookRepository $webhook_repository)
    {
        $this->webhook_repository = $webhook_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateWebhookCommand) {
            throw new \Exception('Provided $command is not instance of CreateWebhookCommand');
        }
    }

    /**
     * @param CreateWebhookCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->webhook_repository->create($action->getWebhookEntity());
    }
}
