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
use Ushahidi\Core\Entity\Set as CollectionEntity;
use Ushahidi\Modules\V5\Requests\CollectionRequest;
use Ushahidi\Modules\V5\Models\Set as CollectionModel;

class CollectionController extends V5Controller
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
        $collection = $this->queryBus->handle(
            FetchCollectionByIdQuery::fromRequest($id, $request)
        );
        $this->authorizeAnyone('view', $collection);
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
        $this->authorizeAnyone('viewAny', CollectionModel::class);
        $collections = $this->queryBus->handle(
            FetchCollectionQuery::fromRequest($request)
        );

        return new CollectionCollection($collections);
    } //end index()


    private function getCollection(int $id, ?array $fields = null, ?array $haydrates = null)
    {
        if (!$fields) {
            $fields = CollectionModel::ALLOWED_FIELDS;
        }
        if (!$haydrates) {
            $haydrates = array_keys(CollectionModel::ALLOWED_RELATIONSHIPS);
        }
        $find_collection_query = new FetchCollectionByIdQuery($id);
        $find_collection_query->addOnlyValues(
            $fields,
            $haydrates,
            CollectionModel::ALLOWED_RELATIONSHIPS,
            CollectionModel::REQUIRED_FIELDS
        );
        return $this->queryBus->handle($find_collection_query);
    }

    /**
     * Create new Collection.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CollectionRequest $request)
    {
        $this->authorize('store', CollectionModel::class);
        // to do fix the create of this
        return $this->show(
            $request,
            $this->commandBus->handle(
                new CreateCollectionCommand(
                    CollectionEntity::buildEntity($request->input())
                )
            )
        );
    } //end store()

    public function update(int $id, CollectionRequest $request)
    {
        $collection = $this->getCollection($id, CollectionModel::ALLOWED_FIELDS, []);
        $collection_entity = CollectionEntity::buildEntity($request->input(), 'update', $collection->toArray());
        $new_collection = new CollectionModel($collection_entity->asArray());
        $new_collection->id = $collection_entity->id;
        $this->authorize('update', $new_collection);
        $this->commandBus->handle(new UpdateCollectionCommand($id, $collection_entity));
        return $this->show($request, $id);
    }

    public function delete(int $id)
    {
        $collection = $this->getCollection($id, ['id'], []);
        $this->authorize('delete', $collection);
        $this->commandBus->handle(new DeleteCollectionCommand($id));
        return response()->json(['result' => ['deleted' => $id]]);
    }
} //end class
