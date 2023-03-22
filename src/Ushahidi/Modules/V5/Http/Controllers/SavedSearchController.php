<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\SavedSearch\Queries\FetchSavedSearchByIdQuery;
use Ushahidi\Modules\V5\Actions\SavedSearch\Queries\FetchSavedSearchQuery;
use Ushahidi\Modules\V5\Http\Resources\SavedSearch\SavedSearchResource;
use Ushahidi\Modules\V5\Http\Resources\SavedSearch\SavedSearchCollection;
use Ushahidi\Modules\V5\Actions\SavedSearch\Commands\CreateSavedSearchCommand;
use Ushahidi\Modules\V5\Actions\SavedSearch\Commands\UpdateSavedSearchCommand;
use Ushahidi\Modules\V5\Actions\SavedSearch\Commands\DeleteSavedSearchCommand;
use Ushahidi\Modules\V5\DTO\SavedSearchSearchFields;
use Ushahidi\Core\Entity\SavedSearch as SavedSearchEntity;
use Ushahidi\Modules\V5\Requests\SavedSearchRequest;
use Ushahidi\Modules\V5\Models\Set as SavedSearch;

class SavedSearchController extends V5Controller
{

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $saved_search = $this->queryBus->handle(new FetchSavedSearchByIdQuery($id));
        return new SavedSearchResource($saved_search);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return SavedSearchCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $surveys = $this->queryBus->handle(
            new FetchSavedSearchQuery(
                $request->query('limit', FetchSavedSearchQuery::DEFAULT_LIMIT),
                $request->query('page', 1),
                $request->query('sortBy', FetchSavedSearchQuery::DEFAULT_SORT_BY),
                $request->query('order', FetchSavedSearchQuery::DEFAULT_ORDER),
                new SavedSearchSearchFields($request)
            )
        );
        return new SavedSearchCollection($surveys);
    } //end index()


    /**
     * Create new Saved Search.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(SavedSearchRequest $request)
    {
        $this->authorize('store', SavedSearch::class);
        return $this->show(
            $this->commandBus->handle(
                new CreateSavedSearchCommand(
                    SavedSearchEntity::buildEntity($request->input())
                )
            )
        );
    } //end store()

    public function update(int $id, SavedSearchRequest $request)
    {
        $saved_search = $this->queryBus->handle(new FetchSavedSearchByIdQuery($id));
        $this->authorize('update', $saved_search);
        $this->commandBus->handle(
            new UpdateSavedSearchCommand(
                $id,
                SavedSearchEntity::buildEntity($request->input(), 'update', $saved_search->toArray())
            )
        );
        return $this->show($id);
    }

    public function delete(int $id)
    {
        $saved_search = $this->queryBus->handle(new FetchSavedSearchByIdQuery($id));
        $this->authorize('delete', $saved_search);
        $this->commandBus->handle(new DeleteSavedSearchCommand($id));
        return response()->json(['result' => ['deleted' => $id]]);
    }
} //end class
