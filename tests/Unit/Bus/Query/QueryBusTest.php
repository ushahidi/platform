<?php

namespace Ushahidi\Tests\Unit\Bus\Query;

use Illuminate\Contracts\Container\Container;
use PHPUnit\Framework\TestCase;
use stdClass;
use App\Bus\Command\Command;
use App\Bus\Command\CommandHandler;
use App\Bus\Handler;
use App\Bus\Query\Query;
use App\Bus\Query\QueryBus;
use App\Bus\Query\QueryHandler;

class QueryBusTest extends TestCase
{
    public function testShouldFailWhenWrongActionIsProvidedToRegister(): void
    {
        // GIVEN
        $mockContainer = $this->createMock(Container::class);
        $handler = $this->createMock(Handler::class);

        // WHEN
        $queryBus = new QueryBus($mockContainer);
        $action = $this->createMock(Command::class);

        // THEN
        $this->expectExceptionMessage(
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                Query::class,
                get_class($action)
            )
        );
        $queryBus->register(get_class($action), get_class($handler));
    }

    public function testShouldFailWhenProvidedHandlerIsIncorrect(): void
    {
        // GIVEN
        $mockContainer = $this->createMock(Container::class);
        $handler = $this->createMock(CommandHandler::class);

        // WHEN
        $queryBus = new QueryBus($mockContainer);
        $action = new class implements Query {
        };

        // THEN
        $this->expectExceptionMessage(
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                QueryHandler::class,
                get_class($handler)
            )
        );
        $queryBus->register(get_class($action), get_class($handler));
    }

    public function testShouldFailWhenWrongQueryIsNotRegistered(): void
    {
        // GIVEN
        $mockContainer = $this->createMock(Container::class);

        // WHEN
        $queryBus = new QueryBus($mockContainer);
        $action = $this->createMock(Query::class);

        // THEN
        $this->expectExceptionMessage(
            sprintf(
                'Invalid argument. %s is not registered',
                get_class($action)
            )
        );
        $queryBus->handle($action);
    }

    public function testShouldSucceedWhenQueryHasItsHandler(): void
    {
        // GIVEN
        $handler = $this->createMock(QueryHandler::class);
        $handler->expects($this->once())->method('__invoke')->willReturn(new stdClass());

        $query = $this->createMock(Query::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('make')->withAnyParameters()->willReturn($handler);

        // WHEN
        $queryBus = new QueryBus($mockContainer);

        // THEN
        $queryBus->register(get_class($query), get_class($handler));
        $queryBus->handle($query);
    }
}
