<?php

namespace Ushahidi\Modules\V5\Actions\Category\Handlers;

use App\Bus\Action;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Category\Commands\StoreCategoryCommand;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;

class StoreCategoryCommandHandler extends V5CommandHandler
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof StoreCategoryCommand) {
            throw new \Exception('Provided $command is not instance of StoreCategoryCommand');
        }
    }

    /**
     * @param StoreCategoryCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action): int
    {
        $this->isSupported($action);

        $slug = $action->getSlug();
        if ($this->categoryRepository->slugExists($slug)) {
            $slug = Category::makeSlug($action->getTag());
        }

        $parentId = $action->getParentId();
        if ($parentId == 0) {
            $parentId = null;
        }

        $user_id = Auth::guard()->user()->id ?? null;

         $category = $this->categoryRepository->store(
             $parentId,
             $user_id,
             ucfirst($action->getTag()),
             $slug,
             $action->getType(),
             $action->getDescription(),
             $action->getColor(),
             $action->getIcon(),
             $action->getPriority(),
             $action->getRole(),
             $action->getDefaultLanguage(),
             $action->getAvailableLanguages()
         );

        $this->saveTranslations(
            $category,
            $category->toArray(),
            $action->getTranslations(),
            $category->id,
            'category'
        );

        return $category->id;
    }
}
