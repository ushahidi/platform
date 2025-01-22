<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Ushahidi\Modules\V5\Http\Resources\Survey\SurveyRoleCollection;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchRolesCanCreateSurveyPostsQuery;
use Ushahidi\Modules\V5\Actions\Survey\Commands\DeleteSurveyRolesBySurveyIDCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateSurveyRoleCommand;
use Ushahidi\Modules\V5\Models\Survey;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyByIdQuery;
use Ushahidi\Modules\V5\Requests\SurveyRoleRequest;

class SurveyRoleController extends V5Controller
{


    private function getSurvey(int $id, ?array $fields = null, ?array $haydrates = null)
    {
        if (!$fields) {
            $fields = Survey::ALLOWED_FIELDS;
        }
        if (!$haydrates) {
            $haydrates = array_keys(Survey::ALLOWED_RELATIONSHIPS);
        }
        $find_survey_query = new FetchSurveyByIdQuery($id);
        $find_survey_query->addOnlyValues(
            $fields,
            $haydrates,
            Survey::ALLOWED_RELATIONSHIPS,
            Survey::REQUIRED_FIELDS
        );
        return $this->queryBus->handle($find_survey_query);
    }

    /**
     * Display the specified resource.
     *
     * @return SurveyRoleCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(int $survey_id, Request $request)
    {
        $survey = $this->getSurvey($survey_id);

        // All access is based on the survey itself, not the role.
       // $this->authorize('show', $survey);
        $resourceCollection = new SurveyRoleCollection(
            $this->queryBus->handle(
                new FetchRolesCanCreateSurveyPostsQuery(
                    $survey_id,
                    $request->query('sortBy', config('paging.default_sort_by')),
                    $request->query('order', config('paging.default_order'))
                )
            )
        );
        return $resourceCollection;
    } //end index()

    /**
     * replace a roles of survey.
     *
     * @param SurveyRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function replace(int $survey_id, SurveyRoleRequest $request)
    {
        $survey = $this->getSurvey($survey_id);

        $this->authorize('update', $survey);

        // to do validate the role is found
        $this->commandBus->handle(new DeleteSurveyRolesBySurveyIDCommand($survey_id));
        $this->commandBus->handle(new CreateSurveyRoleCommand($survey_id, $request->input('roles')));
        return $this->index($survey_id, $request);
    } //end store()
} //end class
