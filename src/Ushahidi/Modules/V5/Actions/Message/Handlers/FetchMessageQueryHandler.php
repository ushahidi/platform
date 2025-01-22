<?php

namespace Ushahidi\Modules\V5\Actions\Message\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Message\Queries\FetchMessageQuery;
use Ushahidi\Modules\V5\Repository\Message\MessageRepository;

class FetchMessageQueryHandler extends AbstractQueryHandler
{
    private $message_repository;

    public function __construct(MessageRepository $message_repository)
    {
        $this->message_repository = $message_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchMessageQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchMessageQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->message_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
