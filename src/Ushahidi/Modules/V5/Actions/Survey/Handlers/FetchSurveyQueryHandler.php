<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5QueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyQuery;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;
use Ushahidi\Modules\V5\Models\Survey;
use App\Bus\Query\QueryBus;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Modules\V5\Actions\Survey\HandleSurveyOnlyParameters;

class FetchSurveyQueryHandler extends V5QueryHandler
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
            get_class($query) === FetchSurveyQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchSurveyQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($action) //: LengthAwarePaginator
    {
        $this->isSupported($action);

        $only_fields =  array_unique(array_merge($action->getFields(), $action->getFieldsForRelationship()));

        $surveys = $this->survey_repository->paginate(
            $action->getPaging(),
            $action->getSearchFields(),
            $only_fields,
            $action->getWithRelationship()
        );

        foreach ($surveys as $survey) {
            $this->addHydrateRelationships(
                $survey,
                $action->getFields(),
                $action->getHydrates()
            );
            $survey->offsetUnset('base_language');
        }
        return $surveys;
    }
}
