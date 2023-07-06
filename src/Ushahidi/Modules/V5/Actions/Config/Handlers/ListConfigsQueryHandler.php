<?php

namespace Ushahidi\Modules\V5\Actions\Config\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use App\Bus\Action;
use App\Bus\Query\QueryBus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ushahidi\Modules\V5\Actions\Config\Queries\ListConfigsQuery;
use Ushahidi\Modules\V5\Repository\Config\ConfigRepository;
use Ushahidi\Modules\V5\Models\Config;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Actions\Config\Queries\FindConfigByNameQuery;

class ListConfigsQueryHandler extends AbstractQueryHandler
{
    private $config_repository;
    private $queryBus;
    public function __construct(ConfigRepository $config_repository, QueryBus $queryBus)
    {
        $this->config_repository = $config_repository;
        $this->queryBus = $queryBus;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof ListConfigsQuery) {
            throw new \InvalidArgumentException('Provided action is not supported');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var ListConfigsQuery $action
         */
        $this->isSupported($action);

        $results = [];
        foreach (Config::AVIALABLE_CONFIG_GROUPS as $group_name) {
            $group_configs = $this->queryBus->handle(new FindConfigByNameQuery($group_name));
            $results[] = $group_configs;
        }
        return collect($results);
    }
}
