<?php

namespace Ushahidi\Modules\V5\Actions\Message\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Message\Queries\FetchMessageByIdQuery;
use Ushahidi\Modules\V5\Repository\Message\MessageRepository;

class FetchMessageByIdQueryHandler extends AbstractQueryHandler
{

    private $message_repository;

    public function __construct(MessageRepository $message_repository)
    {
        $this->message_repository = $message_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchMessageByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchMessageByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        return $this->message_repository->findById($query->getId());
    }
}
