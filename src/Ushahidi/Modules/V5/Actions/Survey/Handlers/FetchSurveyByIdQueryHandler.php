<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use App\Bus\Action;
use Ushahidi\Modules\V5\Actions\V5QueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyByIdQuery;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchTasksBySurveyIdQuery;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchRolesCanCreateSurveyPostsQuery;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;
use Ushahidi\Modules\V5\Models\Survey;
use App\Bus\Query\QueryBus;

class FetchSurveyByIdQueryHandler extends V5QueryHandler
{

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
    public function __invoke($query) //: array
    {
        $only = $this->getSelectFields(
            $query->getFormat(),
            $query->getOnlyFields(),
            Survey::$approved_fields_for_select,
            Survey::$required_fields_for_select
        );
        $this->isSupported($query);
        $survey = $this->survey_repository->findById($query->getId(), $only);

        $this->addHydrateRelationships(
            $survey,
            $only,
            $this->getHydrateRelationshpis(Survey::$relationships, $query->getHydrate())
        );
        $survey->offsetUnset('base_language');

        return $survey;
    }

    private function addHydrateRelationships(&$survey, $only, $hydrate)
    {
        $result = [];
        foreach ($hydrate as $relation) {
            switch ($relation) {
                case 'tasks':
                    $survey->tasks = $this->queryBus->handle(
                        new FetchTasksBySurveyIdQuery(
                            $survey->id,
                            FetchTasksBySurveyIdQuery::DEFAULT_SORT_BY,
                            FetchTasksBySurveyIdQuery::DEFAULT_ORDER
                        )
                    );
                    break;
                case 'translations':
                    $survey->translations = $survey->translations;
                    break;
                case 'enabled_languages':
                    $survey->enabled_languages = [
                        'default' => $survey->base_language,
                        'available' => $survey->translations->groupBy('language')->keys()
                    ];
                    break;
            }
        }
        $this->addCanCreate($survey);
    }

    private function addCanCreate(&$survey)
    {

        $survey_roles = $this->queryBus->handle(
            new FetchRolesCanCreateSurveyPostsQuery(
                $survey->id,
                FetchRolesCanCreateSurveyPostsQuery::DEFAULT_SORT_BY,
                FetchRolesCanCreateSurveyPostsQuery::DEFAULT_ORDER
            )
        );
        $roles = [];
        foreach ($survey_roles as $survey_role) {
            $roles[] = $survey_role->role()->value('name');
        }
        $survey->can_create = $roles;
    }
}
