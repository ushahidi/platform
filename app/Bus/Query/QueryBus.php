<?php

namespace App\Bus\Query;

use App\Bus\Action;
use App\Bus\Bus;
use Illuminate\Contracts\Container\Container;

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
        if (!is_subclass_of($action, Query::class)) {
            throw new \Exception(
                sprintf(
                    'Invalid argument. Expected instance of %s. Got %s',
                    Query::class,
                    $action
                )
            );
        }
    }

    /**
     * @param Action $action
     * @return void
     */
    private function assertQueryRegistered(Action $action): void
    {
        $actionName = get_class($action);

        if (!array_key_exists($actionName, $this->queries)) {
            throw new \Exception(
                sprintf('Invalid argument. %s is not registered.', $actionName)
            );
        }
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
        if (!is_subclass_of($handler, QueryHandler::class)) {
            throw new \Exception(
                sprintf(
                    'Invalid argument. Expected instance of %s. Got %s',
                    QueryHandler::class,
                    $handler
                )
            );
        }
    }
}
