<?php

namespace Ushahidi\Modules\V5\Actions\Role\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Role\Commands\CreateRoleCommand;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;

class CreateRoleCommandHandler extends AbstractCommandHandler
{

    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateRoleCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateRoleCommand $command
     * @return int
     */
    public function __invoke(Action $command) //: int
    {
        $this->isSupported($command);
        $command->setId(
            $this->roleRepository->create($command->getInput())
        );
    }
}
