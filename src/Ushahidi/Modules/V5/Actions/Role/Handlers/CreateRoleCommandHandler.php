<?php

namespace Ushahidi\Modules\V5\Actions\Role\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Role\Commands\CreateRoleCommand;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;
use App\Bus\Command\CommandBus;
use Ushahidi\Modules\V5\Actions\Role\Commands\CreateRolePermissionCommand;

class CreateRoleCommandHandler extends AbstractCommandHandler
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
            get_class($command) === CreateRoleCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateRoleCommand $command
     * @return int
     */
    public function __invoke($command) //: int
    {
        $this->isSupported($command);
        $command->setId(
            $this->roleRepository->create($command->getEntity())
        );

        // add permissions
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
}
