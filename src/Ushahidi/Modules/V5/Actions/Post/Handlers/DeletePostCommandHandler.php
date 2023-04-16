<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Post\Commands\DeletePostCommand;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;

class DeletePostCommandHandler extends V5CommandHandler
{
    private $post_repository;
    public function __construct(PostRepository $post_repository)
    {
        $this->post_repository = $post_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeletePostCommand) {
            throw new \Exception('Provided command is not of type ' . DeletePostCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeletePostCommand $action
         */
         // To do : make it as transaction
        $this->isSupported($action);
        $this->post_repository->delete($action->getId());
        $this->deleteTranslations($action->getId(), 'post');
    }
}
