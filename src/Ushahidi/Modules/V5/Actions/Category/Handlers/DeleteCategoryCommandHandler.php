<?php

namespace Ushahidi\Modules\V5\Actions\Category\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\Category\Commands\DeleteCategoryCommand;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;

class DeleteCategoryCommandHandler extends AbstractCommandHandler
{
    private $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof DeleteCategoryCommand) {
            throw new \Exception('Provided command is not of type ' . DeleteCategoryCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var DeleteCategoryCommand $action
         */
        $this->isSupported($action);

        $category = $this->categoryRepository->findById($action->getId());
        $removed = DB::transaction(function () use ($category) {
            $category->translations()->delete();
            return $category->delete();
        });

        if (!$removed) {
            throw new \Exception('Could not remove category');
        }
    }
}
