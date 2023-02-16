<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchTasksBySurveyIdQuery;
use Ushahidi\Modules\V5\Repository\Survey\TaskRepository;

class FetchTasksBySurveyIdQueryHandler extends AbstractQueryHandler
{
    private $task_repository;

    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchTasksBySurveyIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchSurveyQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke(Action $query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->task_repository->fetch(
            $query->getSortBy(),
            $query->getOrder(),
            $query->getSurveyID()
        );
    }
}
