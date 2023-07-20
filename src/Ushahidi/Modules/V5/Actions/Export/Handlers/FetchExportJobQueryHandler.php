<?php

namespace Ushahidi\Modules\V5\Actions\Export\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Export\Queries\FetchExportJobQuery;
use Ushahidi\Modules\V5\Repository\Export\ExportJobRepository;

class FetchExportJobQueryHandler extends AbstractQueryHandler
{
    private $export_job_repository;

    public function __construct(ExportJobRepository $export_job_repository)
    {
        $this->export_job_repository = $export_job_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchExportJobQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchExportJobQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->export_job_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
