<?php

namespace Ushahidi\Modules\V5\Actions\Tos\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosByIdQuery;
use Ushahidi\Modules\V5\Repository\Tos\TosRepository;

class FetchTosByIdQueryHandler extends AbstractQueryHandler
{

    private $tosRepository;

    public function __construct(TosRepository $tosRepository)
    {
        $this->tosRepository = $tosRepository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchTosByIdQuery::class,
            'Provided query is not supported'
        );
    }
    

    /**
     * @param FetchTosByIdQuery $query
     * @return array
     */
    public function __invoke(Action $query) //: array
    {
        $this->isSupported($query);
        $tos = $this->tosRepository->findById($query->getId());
        return $tos;
    }
}
