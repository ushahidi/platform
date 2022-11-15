<?php

namespace Ushahidi\Modules\V5\Actions\Role\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Role\Queries\FetchRoleQuery;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;

class FetchRoleQueryHandler extends AbstractQueryHandler
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchRoleQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchRoleQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke(Action $query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        $skip = $query->getLimit() * ($query->getPage() - 1);
          return $this->roleRepository->fetch(
              $query->getLimit(),
              $skip,
              $query->getSortBy(),
              $query->getOrder(),
              $query->getSearchData()
          );
    }
}
