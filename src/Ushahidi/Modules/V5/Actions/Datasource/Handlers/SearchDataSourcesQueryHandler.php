<?php

namespace Ushahidi\Modules\V5\Actions\Datasource\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Illuminate\Support\Collection;
use Ushahidi\DataSource\Contracts\DataSource;
use Ushahidi\DataSource\DataSourceManager;
use Ushahidi\Modules\V5\Actions\Datasource\Queries\SearchDataSourcesQuery;

class SearchDataSourcesQueryHandler extends AbstractQueryHandler
{
    private $dataSourceManager;

    public function __construct(DataSourceManager $dataSourceManager)
    {
        $this->dataSourceManager = $dataSourceManager;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof SearchDataSourcesQuery) {
            throw new \Exception('Provided Query not supported');
        }
    }

    /**
     * @param Action|SearchDataSourcesQuery $action
     * @return void
     */
    public function __invoke(Action $action): Collection
    {
        $this->isSupported($action);

        return collect($this->dataSourceManager->getSources())
            ->map(function ($name) {
                return $this->dataSourceManager->getSource($name);
            })
            ->filter(function (DataSource $dataSource) use ($action) {
                if (!$dataSource->isUserConfigurable()) {
                    return false;
                }

                if ($action->getType()) {
                    return in_array($action->getType(), $dataSource->getServices());
                }

                return true;
            })
            ->flatten();
    }
}
