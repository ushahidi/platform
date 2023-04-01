<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionByIdQuery;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionQuery;
use Ushahidi\Modules\V5\Http\Resources\Collection\CollectionResource;
use Ushahidi\Modules\V5\Http\Resources\Collection\CollectionCollection;
use Ushahidi\Modules\V5\Actions\Collection\Commands\CreateCollectionCommand;
use Ushahidi\Modules\V5\Actions\Collection\Commands\UpdateCollectionCommand;
use Ushahidi\Modules\V5\Actions\Collection\Commands\DeleteCollectionCommand;
use Ushahidi\Modules\V5\DTO\CollectionSearchFields;
use Ushahidi\Core\Entity\Set as CollectionEntity;
use Ushahidi\Modules\V5\Requests\CollectionRequest;
use Ushahidi\Modules\V5\Models\Set as Collection;
use Ushahidi\Modules\V5\Policies\CollectionPolicy;

class CollectionController extends V5Controller
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
        $collection = $this->queryBus->handle(new FetchCollectionByIdQuery($id));
        return new CollectionResource($collection);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return CollectionCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $collections = $this->queryBus->handle(
            new FetchCollectionQuery(
                $request->query('limit', FetchCollectionQuery::DEFAULT_LIMIT),
                $request->query('page', 1),
                $request->query('sortBy', FetchCollectionQuery::DEFAULT_SORT_BY),
                $request->query('order', FetchCollectionQuery::DEFAULT_ORDER),
                new CollectionSearchFields($request)
            )
        );
        return new CollectionCollection($collections);
    } //end index()


    /**
     * Create new Collection.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CollectionRequest $request)
    {
        $this->authorize('store', Collection::class);
        return $this->show(
            $this->commandBus->handle(
                new CreateCollectionCommand(
                    CollectionEntity::buildEntity($request->input())
                )
            )
        );
    } //end store()

    public function update(int $id, CollectionRequest $request)
    {
        $saved_search = $this->queryBus->handle(new FetchCollectionByIdQuery($id));
        $this->authorize('update', $saved_search);
        $this->commandBus->handle(
            new UpdateCollectionCommand(
                $id,
                CollectionEntity::buildEntity($request->input(), 'update', $saved_search->toArray())
            )
        );
        return $this->show($id);
    }

    public function delete(int $id)
    {
        $collection = $this->queryBus->handle(new FetchCollectionByIdQuery($id));
        $this->authorize('delete', $collection);
        $this->commandBus->handle(new DeleteCollectionCommand($id));
        return response()->json(['result' => ['deleted' => $id]]);
    }
} //end class
