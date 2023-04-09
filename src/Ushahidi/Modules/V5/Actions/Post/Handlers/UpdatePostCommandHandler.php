<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Post;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Modules\V5\Actions\Post\Commands\UpdatePostCommand;

class UpdatePostCommandHandler extends AbstractCommandHandler
{
    private $post_repository;

    public function __construct(PostRepository $post_repository)
    {
        $this->post_repository = $post_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdatePostCommand) {
            throw new \Exception('Provided $command is not instance of UpdatePostCommand');
        }
    }

    public function __invoke(Action $action): Post
    {
        /**
         * @var UpdatePostCommand $action
         */
        $this->isSupported($action);

        // $this->categoryRepository->update(
        //     $action->getCategoryId(),
        //     $action->getParentId(),
        //     $action->getTag(),
        //     $action->getSlug(),
        //     $action->getType(),
        //     $action->getDescription(),
        //     $action->getColor(),
        //     $action->getIcon(),
        //     $action->getPriority(),
        //     $action->getRole(),
        //     $action->getDefaultLanguage(),
        //     $action->getAvailableLanguages()
        // );

        // return $this->categoryRepository
        //     ->findById($action->getCategoryId());
        return new Post();
    }
}
