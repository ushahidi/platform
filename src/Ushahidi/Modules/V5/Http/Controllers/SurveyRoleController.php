<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Ushahidi\Modules\V5\Http\Resources\SurveyRoleCollection;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchRolesCanCreateSurveyPostsQuery;
use Ushahidi\Modules\V5\Actions\Survey\Commands\DeleteSurveyRolesBySurveyIDCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateSurveyRoleCommand;

class SurveyRoleController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @return SurveyRoleCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(int $survey_id, Request $request)
    {
        //$this->authorizeForCurrentUser('index', SurveyRole::class);
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function replace(int $survey_id, Request $request)
    {
        // to do validate the role is found
        $this->commandBus->handle(new DeleteSurveyRolesBySurveyIDCommand($survey_id));
        $this->commandBus->handle(new CreateSurveyRoleCommand($survey_id, $request->input('roles')));
        return $this->index($survey_id, $request);
    } //end store()
} //end class
