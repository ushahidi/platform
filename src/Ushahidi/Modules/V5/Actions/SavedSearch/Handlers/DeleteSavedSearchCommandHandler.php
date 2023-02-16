<?php

namespace Ushahidi\Modules\V5\Actions\SavedSearch\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\SavedSearch\Commands\DeleteSavedSearchCommand;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as SavedSearchRepository;

class DeleteSavedSearchCommandHandler extends AbstractCommandHandler
{

    private $saved_search_repository;

    public function __construct(SavedSearchRepository $saved_search_repository)
    {
        $this->saved_search_repository = $saved_search_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === DeleteSavedSearchCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param DeleteSavedSearchCommand $command
     * @return int
     */
    public function __invoke($command) //: int

    {
        $this->isSupported($command);
        $this->saved_search_repository->delete($command->getId(), true);
    }
}
