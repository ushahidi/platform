<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Bus\Action;
use App\Bus\Bus;
use Illuminate\Contracts\Container\Container;

class CommandBus implements Bus
{
    /**
     * @var Array<Command>
     */
    private $commands;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->commands = [];
        $this->container = $container;
    }

    /**
     * @param Action $action
     */
    public function handle(Action $action)
    {
        $this->assertIsCommand(get_class($action));
        $this->assertCommandRegistered($action);

        $handler = $this->commands[get_class($action)];

        return $this->container->make($handler)($action);
    }

    /**
     * @param string $action
     * @return void
     */
    private function assertIsCommand(string $action): void
    {
        if (!is_subclass_of($action, Command::class)) {
            throw new \Exception(
                sprintf(
                    'Invalid argument. Expected instance of %s. Got %s',
                    Command::class,
                    $action
                )
            );
        }
    }

    /**
     * @param Action $action
     * @return void
     */
    private function assertCommandRegistered(Action $action): void
    {
        $actionName = get_class($action);

        if (!array_key_exists($actionName, $this->commands)) {
            throw new \Exception(
                sprintf('Invalid argument. %s is not registered.', $actionName)
            );
        }
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
     * @param string $handler
     * @return void
     */
    private function assertIsCommandHandler(string $handler): void
    {
        if (!is_subclass_of($handler, CommandHandler::class)) {
            throw new \Exception(
                sprintf(
                    'Invalid argument. Expected instance of %s. Got %s',
                    CommandHandler::class,
                    $handler
                )
            );
        }
    }
}
