<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\CSV\Queries\FetchCSVByIdQuery;
use Ushahidi\Modules\V5\Repository\CSV\CSVRepository;

class FetchCSVByIdQueryHandler extends AbstractQueryHandler
{

    private $csv_repository;

    public function __construct(CSVRepository $csv_repository)
    {
        $this->csv_repository = $csv_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchCSVByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchCSVByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        return $this->csv_repository->findById($query->getId());
    }
}
