<?php

namespace Ushahidi\Modules\V5\Actions\Media\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Media\Commands\DeleteMediaCommand;
use Ushahidi\Modules\V5\Repository\Media\MediaRepository;

class DeleteMediaCommandHandler extends V5CommandHandler
{
    private $media_repository;
    public function __construct(MediaRepository $media_repository)
    {
        $this->media_repository = $media_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteMediaCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteMediaCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteMediaCommand $action
         */
        $this->isSupported($action);
        $this->media_repository->delete($action->getId());
    }
}
