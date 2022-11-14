<?php

namespace Ushahidi\Modules\V5\Actions\Tos\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Tos\Commands\CreateTosCommand;
use Ushahidi\Modules\V5\Repository\Tos\TosRepository;

class CreateTosCommandHandler extends AbstractCommandHandler
{

    private $tosRepository;

    public function __construct(TosRepository $tosRepository)
    {
        $this->tosRepository = $tosRepository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateTosCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateTosCommand $command
     * @return int
     */
    public function __invoke(Action $command) //: int
    {
        $this->isSupported($command);
        $command->setId(
            $this->tosRepository->create($command->getInput())
        );
    }
}
