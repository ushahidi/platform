<?php

namespace Ushahidi\Modules\V5\Actions\Media\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Media\Media;
use Ushahidi\Modules\V5\Repository\Media\MediaRepository;
use Ushahidi\Modules\V5\Actions\Media\Commands\UpdateMediaCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\MediaLock as Lock;

class UpdateMediaCommandHandler extends AbstractCommandHandler
{
    private $media_repository;

    public function __construct(MediaRepository $media_repository)
    {
        $this->media_repository = $media_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateMediaCommand) {
            throw new \Exception('Provided $command is not instance of UpdateMediaCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateMediaCommand $action
         */
        $this->isSupported($action);

        return $this->media_repository->update($action->getId(), $action->getMediaEntity());
    }
}
