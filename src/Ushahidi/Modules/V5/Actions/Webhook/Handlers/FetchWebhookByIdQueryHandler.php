<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Webhook\Queries\FetchWebhookByIdQuery;
use Ushahidi\Modules\V5\Repository\Webhook\WebhookRepository;

class FetchWebhookByIdQueryHandler extends AbstractQueryHandler
{

    private $webhook_repository;

    public function __construct(WebhookRepository $webhook_repository)
    {
        $this->webhook_repository = $webhook_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchWebhookByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchWebhookByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        return $this->webhook_repository->findById($query->getId());
    }
}
