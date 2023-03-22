<?php

namespace Ushahidi\Modules\V5\Actions\Role\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Role\Commands\UpdateRoleCommand;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;

class UpdateRoleCommandHandler extends AbstractCommandHandler
{

    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === UpdateRoleCommand::class,
            'Provided command not supported'
        );
    }

    /**
     * run the command handler
     * @param UpdateRoleCommand $command
     * @return int
     */
    public function __invoke($command) //: int
    {
        $this->isSupported($command);
        $this->roleRepository->update($command->getId(), $command->getEntity());
    }
}
