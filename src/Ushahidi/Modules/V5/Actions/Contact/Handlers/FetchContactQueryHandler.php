<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Contact\Queries\FetchContactQuery;
use Ushahidi\Modules\V5\Repository\Contact\ContactRepository;

class FetchContactQueryHandler extends AbstractQueryHandler
{
    private $contact_repository;

    public function __construct(ContactRepository $contact_repository)
    {
        $this->contact_repository = $contact_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchContactQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchContactQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->contact_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
