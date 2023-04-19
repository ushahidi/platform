<?php

namespace Ushahidi\Modules\V5\Actions\Permissions\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsByIdQuery;
use Ushahidi\Modules\V5\Repository\Permissions\PermissionsRepository;

class FetchPermissionsByIdQueryHandler extends AbstractQueryHandler
{

    private $permissionsRepository;

    public function __construct(PermissionsRepository $permissionsRepository)
    {
        $this->permissionsRepository = $permissionsRepository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchPermissionsByIdQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchPermissionsByIdQuery $action
     * @return array
     */
    public function __invoke(Action $action) //: array
    {
        $this->isSupported($action);
        $tos = $this->permissionsRepository->findById($action->getId());
        return $tos;
    }
}
