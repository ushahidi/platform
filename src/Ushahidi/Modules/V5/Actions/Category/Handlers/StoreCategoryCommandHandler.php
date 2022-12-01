<?php

namespace Ushahidi\Modules\V5\Actions\Category\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Category\Commands\StoreCategoryCommand;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;

class StoreCategoryCommandHandler extends AbstractCommandHandler
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
     * @return void
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);

    }
}
