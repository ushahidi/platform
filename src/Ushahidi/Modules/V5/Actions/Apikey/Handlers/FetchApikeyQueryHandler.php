<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Apikey\Queries\FetchApikeyQuery;
use Ushahidi\Modules\V5\Repository\Apikey\ApikeyRepository;

class FetchApikeyQueryHandler extends AbstractQueryHandler
{
    private $apikey_repository;

    public function __construct(ApikeyRepository $apikey_repository)
    {
        $this->apikey_repository = $apikey_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchApikeyQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchApikeyQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->apikey_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
