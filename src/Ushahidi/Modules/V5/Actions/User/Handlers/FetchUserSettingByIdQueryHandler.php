<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\User\Queries\FetchUserSettingByIdQuery;
use Ushahidi\Modules\V5\Repository\User\UserSettingRepository;

class FetchUserSettingByIdQueryHandler extends AbstractQueryHandler
{

    private $user_setting_repository;

    public function __construct(UserSettingRepository $user_setting_repository)
    {
        $this->user_setting_repository = $user_setting_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchUserSettingByIdQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchUserSettingByIdQuery $action
     * @return array
     */
    public function __invoke(Action $action) //: array
    {
        $this->isSupported($action);
        return $this->user_setting_repository->findById($action->getId());
    }
}
