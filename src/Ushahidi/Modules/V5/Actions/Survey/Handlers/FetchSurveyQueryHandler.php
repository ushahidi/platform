<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5QueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyQuery;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchTasksBySurveyIdQuery;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchRolesCanCreateSurveyPostsQuery;
use Ushahidi\Modules\V5\Models\Survey;
use App\Bus\Query\QueryBus;
use Illuminate\Pagination\LengthAwarePaginator;

class FetchSurveyQueryHandler extends V5QueryHandler
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
            get_class($query) === FetchSurveyQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchSurveyQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        $skip = $query->getLimit() * ($query->getPage() - 1);
        $only = $this->getSelectFields(
            $query->getFormat(),
            $query->getOnlyFields(),
            Survey::$approved_fields_for_select,
            Survey::$required_fields_for_select
        );

        $surveys = $this->survey_repository->fetch(
            $query->getLimit(),
            $skip,
            $query->getSortBy(),
            $query->getOrder(),
            $query->getSearchFields(),
            $only
        );

        $hydrates = $this->getHydrateRelationshpis(Survey::$relationships, $query->getHydrate());
        foreach ($surveys as $survey) {
            $this->addHydrateRelationships(
                $survey,
                $only,
                $hydrates
            );
            $survey->offsetUnset('base_language');
        }
        return $surveys;
    }


    private function addHydrateRelationships(&$survey, $only, $hydrate)
    {
        $relations = [
            'tasks' => false,
            'translations' => false,
            'enabled_languages' => false,
        ];

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
                    $relations['tasks'] = true;
                    break;
                case 'translations':
                    $relations['translations'] = true;
                    break;
                case 'enabled_languages':
                    $survey->enabled_languages = [
                        'default' => $survey->base_language,
                        'available' => $survey->translations->groupBy('language')->keys()
                    ];
                    $relations['enabled_languages'] = true;
                    break;
            }
        }

        if (!$relations['tasks']) {
            $survey->tasks = null;
        }
        if (!$relations['translations']) {
            $survey->translations = null;
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
