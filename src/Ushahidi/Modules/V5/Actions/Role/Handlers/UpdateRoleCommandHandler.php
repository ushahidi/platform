<?php

namespace Ushahidi\Modules\V5\Actions\Role\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Role\Commands\UpdateRoleCommand;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;
use App\Bus\Command\CommandBus;
use Ushahidi\Modules\V5\Actions\Role\Commands\CreateRolePermissionCommand;
use Ushahidi\Modules\V5\Actions\Role\Commands\DeleteRolePermissionByRoleCommand;

class UpdateRoleCommandHandler extends AbstractCommandHandler
{

    private $roleRepository;
    private $commandBus;

    public function __construct(CommandBus $commandBus, RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->commandBus = $commandBus;
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

        // update permissions
        $this->deleteRolePermissionsByRole($command->getEntity()->name);
        if ($command->getPermissions()) {
            $this->addRolePermissions(
                $command->getPermissions(),
                $command->getEntity()->name
            );
        }
    }

    private function addRolePermissions(array $permissions, $role)
    {
        foreach ($permissions as $permission_name) {
            if (trim($permission_name) != "") { // ignore empty values
                $this->commandBus->handle(
                    new CreateRolePermissionCommand(
                        $role,
                        $permission_name
                    )
                );
            }
        }
    }
    private function deleteRolePermissionsByRole($role)
    {
        $this->commandBus->handle(
            new DeleteRolePermissionByRoleCommand($role)
        );
    }
}
