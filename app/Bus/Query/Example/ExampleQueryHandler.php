<?php

namespace App\Bus\Query\Example;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;

class ExampleQueryHandler extends AbstractQueryHandler
{
    /**
     * @var FakeMessageRepository
     */
    private $repository;

    /**
     * @param FakeMessageRepository $repository
     */
    public function __construct(FakeMessageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Action|ExampleQuery $action
     * @return string
     */
    public function __invoke(Action $action): string
    {
        $this->isSupported($action);

        return $this->repository
            ->findByIndex(
                $action->getIndex()
            );
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === ExampleQuery::class,
            'Provided query is not supported'
        );
    }
}
