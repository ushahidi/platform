<?php

namespace Ushahidi\Modules\V5\Actions\Media\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Modules\V5\Repository\Media\MediaRepository;
use Ushahidi\Modules\V5\Actions\Media\Commands\UpdateMediaCaptionCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\MediaLock as Lock;

class UpdateMediaCaptionCommandHandler extends AbstractCommandHandler
{
    private $media_repository;

    public function __construct(MediaRepository $media_repository)
    {
        $this->media_repository = $media_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateMediaCaptionCommand) {
            throw new \Exception('Provided $command is not instance of UpdateMediaCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateMediaCaptionCommand $action
         */
        $this->isSupported($action);

        return $this->media_repository->update($action->getMediaEntity()->getId(), $action->getMediaEntity());
    }
}
