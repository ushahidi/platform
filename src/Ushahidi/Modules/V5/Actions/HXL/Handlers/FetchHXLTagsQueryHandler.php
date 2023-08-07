<?php

namespace Ushahidi\Modules\V5\Actions\HXL\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLTagsQuery;
use Ushahidi\Modules\V5\Repository\HXL\HXLRepository;

class FetchHXLTagsQueryHandler extends AbstractQueryHandler
{
    private $hxl_repository;

    public function __construct(HXLRepository $hxl_repository)
    {
        $this->hxl_repository = $hxl_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchHXLTagsQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchHXLTagsQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->hxl_repository->fetchTags(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
