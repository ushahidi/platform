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
     * @param FetchUserQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke(Action $query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        $skip = $query->getLimit() * ($query->getPage() - 1);
        return $this->user_repository->fetch(
            $query->getLimit(),
            $skip,
            $query->getSortBy(),
            $query->getOrder(),
            $query->getUserSearchFields()
        );
    }
}
