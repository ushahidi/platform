<?php

namespace Tests\Unit\Bus\Command;

use App\Bus\Command\Command;
use App\Bus\Command\CommandBus;
use App\Bus\Command\CommandHandler;
use App\Bus\Handler;
use App\Bus\Query\Query;
use App\Bus\Query\QueryHandler;
use Illuminate\Contracts\Container\Container;
use PHPUnit\Framework\TestCase;

class CommandBusTest extends TestCase
{
    public function testShouldFailWhenWrongActionIsProvidedToRegister(): void
    {
        // GIVEN
        $mockContainer = $this->createMock(Container::class);
        $mockHandler = $this->createMock(Handler::class);

        // WHEN
        $commandBus = new CommandBus($mockContainer);
        $action = $this->createMock(Query::class);

        // THEN
        $this->expectExceptionMessage(
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                Command::class,
                get_class($action)
            )
        );
        $commandBus->register(get_class($action), get_class($mockHandler));
    }

    public function testShouldFailWhenProvidedHandlerIsIncorrect(): void
    {
        // GIVEN
        $mockContainer = $this->createMock(Container::class);
        $handler = $this->createMock(QueryHandler::class);

        // WHEN
        $commandBus = new CommandBus($mockContainer);
        $action = new class implements Command {
        };

        // THEN
        $this->expectExceptionMessage(
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                CommandHandler::class,
                get_class($handler)
            )
        );
        $commandBus->register(get_class($action), get_class($handler));
    }

    public function testShouldFailWhenWrongCommandIsNotRegistered(): void
    {
        // GIVEN
        $mockContainer = $this->createMock(Container::class);

        // WHEN
        $commandBus = new CommandBus($mockContainer);
        $action = $this->createMock(Command::class);

        // THEN
        $this->expectExceptionMessage(
            sprintf(
                'Invalid argument. %s is not registered.',
                get_class($action)
            )
        );
        $commandBus->handle($action);
    }

    public function testShouldSucceedWhenCommandHasItsHandler(): void
    {
        // GIVEN
        $handler = $this->createMock(CommandHandler::class);
        $handler->expects($this->once())->method('__invoke');

        $command = $this->createMock(Command::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('make')->withAnyParameters()->willReturn($handler);

        // WHEN
        $commandBus = new CommandBus($mockContainer);

        // THEN
        $commandBus->register(get_class($command), get_class($handler));
        $commandBus->handle($command);
    }
}
