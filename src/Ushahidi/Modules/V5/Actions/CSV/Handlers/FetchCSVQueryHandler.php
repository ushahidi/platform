<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\CSV\Queries\FetchCSVQuery;
use Ushahidi\Modules\V5\Repository\CSV\CSVRepository;

class FetchCSVQueryHandler extends AbstractQueryHandler
{
    private $csv_repository;

    public function __construct(CSVRepository $csv_repository)
    {
        $this->csv_repository = $csv_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchCSVQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchCSVQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->csv_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
