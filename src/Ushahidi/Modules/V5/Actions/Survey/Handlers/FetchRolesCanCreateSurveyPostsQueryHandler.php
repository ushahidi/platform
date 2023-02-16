<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchRolesCanCreateSurveyPostsQuery;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;

class FetchRolesCanCreateSurveyPostsQueryHandler extends AbstractQueryHandler
{
    private $survey_repository;

    public function __construct(SurveyRepository $survey_repository)
    {
        $this->survey_repository = $survey_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchRolesCanCreateSurveyPostsQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchRolesCanCreateSurveyPostsQuery $query
     * @return array
     */
    public function __invoke($query)
    {
        $this->isSupported($query);
        return $this->survey_repository->getRolesCanCreatePosts($query->getSurveyId());
    }
}
