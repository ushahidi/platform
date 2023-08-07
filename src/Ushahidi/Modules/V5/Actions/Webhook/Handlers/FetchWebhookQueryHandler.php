<?php

namespace Ushahidi\Modules\V5\Actions\Webhook\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Webhook\Queries\FetchWebhookQuery;
use Ushahidi\Modules\V5\Repository\Webhook\WebhookRepository;

class FetchWebhookQueryHandler extends AbstractQueryHandler
{
    private $webhook_repository;

    public function __construct(WebhookRepository $webhook_repository)
    {
        $this->webhook_repository = $webhook_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchWebhookQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchWebhookQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->webhook_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
