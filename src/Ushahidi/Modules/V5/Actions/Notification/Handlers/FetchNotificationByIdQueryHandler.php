<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Notification\Queries\FetchNotificationByIdQuery;
use Ushahidi\Modules\V5\Repository\Notification\NotificationRepository;

class FetchNotificationByIdQueryHandler extends AbstractQueryHandler
{

    private $notification_repository;

    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchNotificationByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchNotificationByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        return $this->notification_repository->findById($query->getId());
    }
}
