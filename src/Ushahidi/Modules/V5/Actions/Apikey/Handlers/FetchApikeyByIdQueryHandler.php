<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Apikey\Queries\FetchApikeyByIdQuery;
use Ushahidi\Modules\V5\Repository\Apikey\ApikeyRepository;

class FetchApikeyByIdQueryHandler extends AbstractQueryHandler
{

    private $apikey_repository;

    public function __construct(ApikeyRepository $apikey_repository)
    {
        $this->apikey_repository = $apikey_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchApikeyByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchApikeyByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        return $this->apikey_repository->findById($query->getId());
    }
}
