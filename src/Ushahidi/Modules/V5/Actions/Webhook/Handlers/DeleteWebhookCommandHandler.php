<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Webhook\Commands\DeleteWebhookCommand;
use Ushahidi\Modules\V5\Repository\Webhook\WebhookRepository;

class DeleteWebhookCommandHandler extends V5CommandHandler
{
    private $webhook_repository;
    public function __construct(WebhookRepository $webhook_repository)
    {
        $this->webhook_repository = $webhook_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteWebhookCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteWebhookCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteWebhookCommand $action
         */
        $this->isSupported($action);
        $this->webhook_repository->delete($action->getId());
    }
}
