<?php

namespace App\Bus\Query;

use App\Bus\Action;
use App\Bus\Bus;
use Illuminate\Contracts\Container\Container;
use Webmozart\Assert\Assert;

class QueryBus implements Bus
{
    /**
     * @var Array<Query>
     */
    private $queries;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->queries = [];
        $this->container = $container;
    }

    public function handle(Action $action): object
    {
        $this->assertIsQuery(get_class($action));
        $this->assertQueryRegistered($action);

        $handler = $this->queries[get_class($action)];

        return $this->container->make($handler)($action);
    }

    /**
     * @param string $action
     * @return void
     */
    private function assertIsQuery(string $action): void
    {
        Assert::true(
            is_subclass_of($action, Query::class),
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                Query::class,
                $action
            )
        );
    }

    /**
     * @param Action $action
     * @return void
     */
    private function assertQueryRegistered(Action $action): void
    {
        $actionName = get_class($action);
        Assert::true(
            array_key_exists($actionName, $this->queries),
            sprintf('Invalid argument. %s is not registered.', $actionName)
        );
    }

    public function register(string $action, string $handler): void
    {
        $this->assertIsQuery($action);
        $this->assertIsQueryHandler($handler);

        $this->queries[$action] = $handler;
    }

    /**
     * @param string $handler
     * @return void
     */
    private function assertIsQueryHandler(string $handler): void
    {
        Assert::true(
            is_subclass_of($handler, QueryHandler::clasS),
            sprintf(
                'Invalid argument. Expected instance of %s. Got %s',
                QueryHandler::class,
                $handler
            )
        );
    }
}
