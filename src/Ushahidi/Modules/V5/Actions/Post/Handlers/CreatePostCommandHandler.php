<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Post\Commands\CreatePostCommand;
use Ushahidi\Modules\V5\Models\Post;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;

class CreatePostCommandHandler extends AbstractCommandHandler
{
    private $post_repository;

    public function __construct(PostRepository $post_repository)
    {
        $this->post_repository = $post_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreatePostCommand) {
            throw new \Exception('Provided $command is not instance of CreatePostCommand');
        }
    }

    /**
     * @param CreatePostCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action): int
    {
        $this->isSupported($action);

    //     $slug = $action->getSlug();
    //     if ($this->categoryRepository->slugExists($slug)) {
    //         $slug = Category::makeSlug($action->getTag());
    //     }

    //     $parentId = $action->getParentId();
    //     if ($parentId == 0) {
    //         $parentId = null;
    //     }

    //     return $this->categoryRepository->store(
    //         $parentId,
    //         ucfirst($action->getTag()),
    //         $slug,
    //         $action->getType(),
    //         $action->getDescription(),
    //         $action->getColor(),
    //         $action->getIcon(),
    //         $action->getPriority(),
    //         $action->getRole(),
    //         $action->getDefaultLanguage(),
    //         $action->getAvailableLanguages()
    //     );
        return 0;
    }
}
