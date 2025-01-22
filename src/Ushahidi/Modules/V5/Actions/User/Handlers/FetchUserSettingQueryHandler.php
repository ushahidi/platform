<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserSettingQuery;
use Ushahidi\Modules\V5\Repository\User\UserSettingRepository;

class FetchUserSettingQueryHandler extends AbstractQueryHandler
{
    private $user_setting_repository;

    public function __construct(UserSettingRepository $user_setting_repository)
    {
        $this->user_setting_repository = $user_setting_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchUserSettingQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchUserSettingQuery $action
     * @return LengthAwarePaginator
     */
    public function __invoke(Action $action) //: LengthAwarePaginator
    {
        $this->isSupported($action);
        $skip = $action->getLimit() * ($action->getPage() - 1);
        return $this->user_setting_repository->fetch(
            $action->getUserId(),
            $action->getLimit(),
            $skip,
            $action->getSortBy(),
            $action->getOrder()
        );
    }
}
