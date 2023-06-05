<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchContactByIdQuery;
use Ushahidi\Modules\V5\Repository\Contact\ContactRepository;

class FetchCollectionByIdQueryHandler extends AbstractQueryHandler
{

    private $contact_repository;

    public function __construct(ContactRepository $contact_repository)
    {
        $this->contact_repository = $contact_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchContactByIdQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchContactByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        return $this->contact_repository->findById($query->getId());
    }
}
