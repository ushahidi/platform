<?php

namespace Ushahidi\Modules\V5\Actions\Media\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Media\Commands\CreateMediaCommand;
use Ushahidi\Modules\V5\Repository\Media\MediaRepository;
use Ushahidi\Core\Entity\Media as MediaEntity;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Media;

class CreateMediaCommandHandler extends V5CommandHandler
{
    private $media_repository;

    public function __construct(MediaRepository $media_repository)
    {
        $this->media_repository = $media_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateMediaCommand) {
            throw new \Exception('Provided $command is not instance of CreateMediaCommand');
        }
    }

    /**
     * @param CreateMediaCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        // $this->validateFileData($action->getMediaEntity());
        return $this->media_repository->create($action->getMediaEntity());
    }

    protected function validateFileData(MediaEntity $file)
    {
        $max_bytes = env('IMAGE_MAX_SIZE', '4194304');
        $errors = [];
        if (!in_array($file->mime, ['image/gif', 'image/jpg', 'image/jpeg', 'image/png'])) {
            $errors['file'][] = "File type not supported. Please upload an image file.";
        }
        if ($file->o_size <= 0 || $file->o_size > $max_bytes) {
            $size_in_mb = floor(($max_bytes / 1024) / 1024);
            $errors['file'][] = "The file size should be less than $size_in_mb MB";
        }
        if (count($errors)) {
            $this->failedValidation($errors);
        }
    }
}
