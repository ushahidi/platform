<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserByIdQuery;
use Ushahidi\Modules\V5\Repository\User\UserRepository;

class FetchUserByIdQueryHandler extends AbstractQueryHandler
{

    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchUserByIdQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchUserByIdQuery $action
     * @return array
     */
    public function __invoke(Action $action) //: array
    {
        $this->isSupported($action);
        return $this->user_repository->findById($action->getId());
    }
}
