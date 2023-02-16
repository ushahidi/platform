<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use App\Bus\Action;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Survey\Commands\DeleteTasksCommand;
use Ushahidi\Modules\V5\Repository\Survey\TaskRepository;

class DeleteTasksCommandHandler extends V5CommandHandler
{

    private $task_repository;

    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === DeleteTasksCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param DeleteTasksCommand $command
     * @return int
     */
    public function __invoke($command) //: int

    {
        $this->isSupported($command);
        $this->deleteListTranslations($command->getTaskIds(), 'task');
        //TO DO : get the fields of removed tasks to remove thier translations
        $this->task_repository->deleteTasks($command->getTaskIds());
    }
}
