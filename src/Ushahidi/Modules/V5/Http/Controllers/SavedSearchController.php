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
use Ushahidi\Core\Entity\SavedSearch as SavedSearchEntity;
use Ushahidi\Modules\V5\Requests\SavedSearchRequest;
use Ushahidi\Modules\V5\Models\Set as SavedSearch;

class SavedSearchController extends V5Controller
{

    /**
     * Display the specified resource.
     * @param Request $request
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, int $id)
    {
        $saved_search = $this->queryBus->handle(
            FetchSavedSearchByIdQuery::fromRequest($id, $request)
        );
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
        $saved_searches = $this->queryBus->handle(
            FetchSavedSearchQuery::fromRequest($request)
        );
        return new SavedSearchCollection($saved_searches);
    } //end index()


    private function getSavedSearch(int $id, ?array $fields = null, ?array $haydrates = null)
    {
        if (!$fields) {
            $fields = SavedSearch::ALLOWED_FIELDS;
        }
        if (!$haydrates) {
            $haydrates = array_keys(SavedSearch::ALLOWED_RELATIONSHIPS);
        }
        $find_saved_search_query = new FetchSavedSearchByIdQuery($id);
        $find_saved_search_query->addOnlyValues(
            $fields,
            $haydrates,
            SavedSearch::ALLOWED_RELATIONSHIPS,
            SavedSearch::REQUIRED_FIELDS
        );
        return $this->queryBus->handle($find_saved_search_query);
    }

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
            $request,
            $this->commandBus->handle(
                new CreateSavedSearchCommand(
                    SavedSearchEntity::buildEntity($request->input())
                )
            )
        );
    } //end store()

    public function update(int $id, SavedSearchRequest $request)
    {
        $saved_search = $this->getSavedSearch($id, SavedSearch::ALLOWED_FIELDS, []);
        $saved_search_entity = SavedSearchEntity::buildEntity($request->input(), 'update', $saved_search->toArray());
        $new_saved_search = new SavedSearch($saved_search_entity->asArray());
        $new_saved_search->id = $saved_search_entity->id;
        $this->authorize('update', $new_saved_search);
        $this->commandBus->handle(new UpdateSavedSearchCommand($id, $saved_search_entity));
        return $this->show($request, $id);
    }

    public function delete(int $id)
    {
        $saved_search = $this->getSavedSearch($id, ['id'], []);
        $this->authorize('delete', $saved_search);
        $this->commandBus->handle(new DeleteSavedSearchCommand($id));
        return response()->json(['result' => ['deleted' => $id]]);
    }
} //end class
