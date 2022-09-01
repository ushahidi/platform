<?php

declare(strict_types=1);

namespace Ushahidi\App\Bus\Command;

use Ushahidi\App\Bus\Action;
use Ushahidi\App\Bus\Bus;
use Ushahidi\App\Bus\Handler;

class CommandBus implements Bus
{
    /**
     * @var Array<Command>
     */
    private $commands;

    public function __construct()
    {
        $this->commands = [];
    }

    /**
     * @param Action $action
     * @return void
     */
    public function handle(Action $action): void
    {
        $this->assertIsCommand(get_class($action));
        $this->assertCommandRegistered($action);

        $handler = $this->commands[get_class($action)];

        resolve($handler)($action);
    }

    /**
     * @param string $action
     * @param string $handler
     * @return void
     */
    public function register(string $action, string $handler): void
    {
        $this->assertIsCommand($action);
        $this->assertIsCommandHandler($handler);

        $this->commands[$action] = $handler;
    }

    /**
     * @param string $action
     * @return void
     */
    private function assertIsCommand(string $action): void
    {
        assert(
            is_subclass_of($action, Command::class),
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                Command::class,
                $action
            )
        );
    }

    /**
     * @param string $handler
     * @return void
     */
    private function assertIsCommandHandler(string $handler): void
    {
        assert(
            is_subclass_of($handler, CommandHandler::class),
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                CommandHandler::class,
                $handler
            )
        );
    }

    /**
     * @param Action $action
     * @return void
     */
    private function assertCommandRegistered(Action $action): void
    {
        $actionName = get_class($action);
        assert(
            array_key_exists($actionName, $this->commands),
            sprintf('Invalid argument. %s is not registered.', $actionName)
        );
    }
}
