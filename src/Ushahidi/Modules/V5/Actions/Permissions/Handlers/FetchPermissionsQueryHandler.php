<?php

namespace Ushahidi\Modules\V5\Actions\Permissions\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsQuery;
use Ushahidi\Modules\V5\Repository\Permissions\PermissionsRepository;

class FetchPermissionsQueryHandler extends AbstractQueryHandler
{
    private $permissionsRepository;

    public function __construct(PermissionsRepository $permissionsRepository)
    {
        $this->permissionsRepository = $permissionsRepository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchPermissionsQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchPermissionsQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke(Action $query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        $skip = $query->getLimit() * ($query->getPage() - 1);
          return $this->permissionsRepository->fetch(
              $query->getLimit(),
              $skip,
              $query->getSortBy(),
              $query->getOrder(),
              $query->getSearchData()
          );
    }
}
