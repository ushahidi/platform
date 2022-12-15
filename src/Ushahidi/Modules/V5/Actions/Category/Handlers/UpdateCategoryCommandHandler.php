<?php

namespace Ushahidi\Modules\V5\Actions\Category\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;
use Ushahidi\Modules\V5\Actions\Category\Commands\UpdateCategoryCommand;

class UpdateCategoryCommandHandler extends AbstractCommandHandler
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    protected function isSupported(Command $command): void
    {
        // TODO: Implement
    }

    public function __invoke(Action $action): Category
    {
        /**
         * @var UpdateCategoryCommand $action
         */
        $this->isSupported($action);

        $this->categoryRepository->update(
            $action->getCategoryId(),
            $action->getParentId(),
            $action->getTag(),
            $action->getSlug(),
            $action->getType(),
            $action->getDescription(),
            $action->getColor(),
            $action->getIcon(),
            $action->getPriority(),
            $action->getRole(),
            $action->getDefaultLanguage(),
            $action->getAvailableLanguages()
        );

        return $this->categoryRepository
            ->fetchByIdOrFail($action->getCategoryId());
    }
}
