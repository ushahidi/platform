<?php

namespace Ushahidi\Modules\V5\Actions\Tos\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosQuery;
use Ushahidi\Modules\V5\Repository\Tos\TosRepository;

class FetchTosQueryHandler extends AbstractQueryHandler
{
    private $tosRepository;

    public function __construct(TosRepository $tosRepository)
    {
        $this->tosRepository = $tosRepository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchTosQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchTosQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke(Action $query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        $skip = $query->getLimit() * ($query->getPage() - 1);
          return $this->tosRepository->fetch(
              $query->getLimit(),
              $skip,
              $query->getSortBy(),
              $query->getOrder()
          );
    }
}
