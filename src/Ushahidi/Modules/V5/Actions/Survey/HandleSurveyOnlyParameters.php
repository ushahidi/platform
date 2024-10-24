<?php

namespace Ushahidi\Modules\V5\Actions\Survey;

use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchTasksBySurveyIdQuery;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchRolesCanCreateSurveyPostsQuery;

trait HandleSurveyOnlyParameters
{
    private function addHydrateRelationships(&$survey, $only, $hydrate)
    {
        $relations = [
            'tasks' => false,
            'translations' => false,
            'enabled_languages' => false,
        ];

        foreach ($hydrate as $relation) {
            switch ($relation) {
                case 'can_create':
                    $this->addCanCreate($survey);
                    break;
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
