<?php

namespace Ushahidi\Modules\V5\Actions\Role\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Role\Commands\DeleteRolePermissionByRoleCommand;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;

class DeleteRolePermissionByRoleCommandHandler extends AbstractCommandHandler
{

    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === DeleteRolePermissionByRoleCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param DeleteRolePermissionByRoleCommand $command
     * @return int
     */
    public function __invoke(Action $command)
    {
        $this->isSupported($command);
        $this->roleRepository->deleteRolePermissionByRole($command->getRole());
    }
}
