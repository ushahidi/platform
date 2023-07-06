<?php

namespace Ushahidi\Modules\V5\Actions\Notification\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Notification\Queries\FetchNotificationQuery;
use Ushahidi\Modules\V5\Repository\Notification\NotificationRepository;

class FetchNotificationQueryHandler extends AbstractQueryHandler
{
    private $notification_repository;

    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchNotificationQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchNotificationQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->notification_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
