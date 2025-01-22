<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserQuery;
use Ushahidi\Modules\V5\Repository\User\UserRepository;

class FetchUserQueryHandler extends AbstractQueryHandler
{
    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchUserQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchUserQuery $action
     * @return LengthAwarePaginator
     */
    public function __invoke(Action $action) //: LengthAwarePaginator
    {
        $this->isSupported($action);
        $skip = $action->getLimit() * ($action->getPage() - 1);
        return $this->user_repository->fetch(
            $action->getLimit(),
            $skip,
            $action->getSortBy(),
            $action->getOrder(),
            $action->getUserSearchFields()
        );
    }
}
