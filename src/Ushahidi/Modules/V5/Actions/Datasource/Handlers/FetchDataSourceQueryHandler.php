<?php

namespace Ushahidi\Modules\V5\Actions\Datasource\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\DataSource\Contracts\DataSource;
use Ushahidi\DataSource\DataSourceManager;
use Ushahidi\Modules\V5\Actions\Datasource\Queries\FetchDataSourceQuery;

class FetchDataSourceQueryHandler extends AbstractQueryHandler
{
    private $dataSourceManager;

    public function __construct(DataSourceManager $dataSourceManager)
    {
        $this->dataSourceManager = $dataSourceManager;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof FetchDataSourceQuery) {
            throw new \Exception(
                sprintf(
                    'Provided Query %s is not supported. Expected: %s',
                    get_class($query),
                    FetchDataSourceQuery::class
                )
            );
        }
    }

    /**
     * @param Query|Action|FetchDataSourceQuery $action
     * @return void
     */
    public function __invoke(Action $action): DataSource
    {
        $this->isSupported($action);

        return $this->dataSourceManager->getSource($action->getSource());
    }
}
