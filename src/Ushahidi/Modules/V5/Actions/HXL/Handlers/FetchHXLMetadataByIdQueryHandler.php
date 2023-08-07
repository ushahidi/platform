<?php

namespace Ushahidi\Modules\V5\Actions\HXL\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\HXL\Queries\FetchHXLMetadataByIdQuery;
use Ushahidi\Modules\V5\Repository\HXL\HXLRepository;

class FetchHXLMetadataByIdQueryHandler extends AbstractQueryHandler
{

    private $hxl_repository;

    public function __construct(HXLRepository $hxl_repository)
    {
        $this->hxl_repository = $hxl_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchHXLMetadataByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchHXLMetadataByIdQuery $query
     * @return array
     */
    public function __invoke($query)
    {
        $this->isSupported($query);
        return $this->hxl_repository->fetchHXLMetadataById($query->getId());
    }
}
