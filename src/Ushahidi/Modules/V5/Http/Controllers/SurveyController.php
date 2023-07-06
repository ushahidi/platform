<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Models\Survey;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyByIdQuery;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyQuery;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateSurveyCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\UpdateSurveyCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\DeleteSurveyCommand;
use Ushahidi\Modules\V5\Http\Resources\Survey\SurveyCollection;
use Ushahidi\Modules\V5\Http\Resources\Survey\SurveyResource;
use Ushahidi\Modules\V5\DTO\SurveySearchFields;
use Ushahidi\Core\Entity\Form as SurveyEntity;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Http\Resources\TranslationCollection;
use Ushahidi\Modules\V5\Requests\SurveyRequest;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyStatsQuery;
use Ushahidi\Modules\V5\DTO\SurveyStatesSearchFields;
use Ushahidi\Modules\V5\Http\Resources\Survey\SurveyStateResource;

class SurveyController extends V5Controller
{
    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, int $id)
    {
        $survey = $this->queryBus->handle(
            new FetchSurveyByIdQuery(
                $id,
                $request->input('formater') ?? null,
                $request->input('only') ?? null,
                $request->input('hydrate') ?? null
            )
        );

       // $this->authorize('show', $survey);
        return new SurveyResource($survey);
    } //end show()


    /**
     * Display the specified resource.
     *
     * @return SurveyCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
       // $this->authorize('index', new Survey());

        $surveys = $this->queryBus->handle(
            new FetchSurveyQuery(
                $request->query('limit', FetchSurveyQuery::DEFAULT_LIMIT),
                $request->query('page', 1),
                $request->query('sortBy', "id"),
                $request->query('order', FetchSurveyQuery::DEFAULT_ORDER),
                new SurveySearchFields($request),
                $request->input('formater') ?? null,
                $request->input('only') ?? null,
                $request->input('hydrate') ?? null
            )
        );
        return new SurveyCollection($surveys);
    } //end index()

    /**
     * Display the specified resource.
     *
     * @TODO   transactions =)
     * @param SurveyRequest $request
     * @return SurveyResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(SurveyRequest $request)
    {
        $authorizer = service('authorizer.form');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        $survey = new Survey();
        if ($user) {
            $this->authorize('store', Survey::class);
        }

        $survey_id = $this->commandBus->handle(
            new CreateSurveyCommand(
                SurveyEntity::buildEntity($request->input()),
                $request->input('tasks') ?? [],
                $request->input('translations') ?? []
            )
        );

        return $this->show($request, $survey_id);
    } //end store()

    /**
     * Display the specified resource.
     *
     * @TODO   transactions =)
     * @param integer $id
     * @param SurveyRequest $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, SurveyRequest $request)
    {
        $survey = $this->queryBus->handle(new FetchSurveyByIdQuery($id));

        $this->authorize('update', $survey);
        if (!$survey) {
            return self::make404();
        }

        $current_task_ids = $survey->tasks->modelKeys();
        $this->commandBus->handle(
            new UpdateSurveyCommand(
                $id,
                SurveyEntity::buildEntity($request->input(), 'update', $survey->toArray()),
                $request->input('tasks') ?? [],
                $request->input('translations') ?? [],
                $current_task_ids ?? []
            )
        );
        return $this->show($request, $id);
    } //end update()
    /**
     * @param integer $id
     */
    public function delete(int $id, Request $request)
    {
        $survey = $this->queryBus->handle(new FetchSurveyByIdQuery($id, null, 'id', 'tasks'));
        $this->authorize('delete', $survey);
        $task_ids = $survey->tasks->modelKeys();
        $field_ids = $survey->tasks->map(function ($task, $key) use (&$field_ids) {
            return $task->fields->modelKeys();
        })->flatten();
        $this->commandBus->handle(new DeleteSurveyCommand($id, $task_ids ?? [], $field_ids->toArray() ?? []));
        return response()->json(['result' => ['deleted' => $id]]);
    } //end delete()


    public function stats(int $id, Request $request)
    {
       // $this->authorize('stats', new Survey());

        $stats = $this->queryBus->handle(new FetchSurveyStatsQuery($id, new SurveyStatesSearchFields($request)));
        return new SurveyStateResource($stats);
    } //end stats()
} //end class
