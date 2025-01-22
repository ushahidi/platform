<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5QueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyByIdQuery;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;
use Ushahidi\Modules\V5\Models\Survey;
use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Actions\Survey\HandleSurveyOnlyParameters;

class FetchSurveyByIdQueryHandler extends V5QueryHandler
{

    use HandleSurveyOnlyParameters;

    private $survey_repository;
    private $queryBus;

    public function __construct(QueryBus $queryBus, SurveyRepository $survey_repository)
    {
        $this->survey_repository = $survey_repository;
        $this->queryBus = $queryBus;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchSurveyByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchSurveyByIdQuery $query
     * @return Survey
     */
    public function __invoke($action) //: array
    {
        $this->isSupported($action);
        $survey = $this->survey_repository->findById(
            $action->getId(),
            array_unique(array_merge(
                $action->getFields(),
                $action->getFieldsForRelationship()
            )),
            $action->getWithRelationship()
        );
        $this->addHydrateRelationships(
            $survey,
            $action->getFields(),
            $action->getHydrates()
        );
        $survey->offsetUnset('base_language');

        return $survey;
    }
}
