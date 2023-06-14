<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Layer\Queries\FetchLayerByIdQuery;
use Ushahidi\Modules\V5\Actions\Layer\Queries\FetchLayerQuery;
use Ushahidi\Modules\V5\Http\Resources\Layer\LayerResource;
use Ushahidi\Modules\V5\Http\Resources\Layer\LayerCollection;
use Ushahidi\Modules\V5\Actions\Layer\Commands\CreateLayerCommand;
use Ushahidi\Modules\V5\Actions\Layer\Commands\UpdateLayerCommand;
use Ushahidi\Modules\V5\Actions\Layer\Commands\DeleteLayerCommand;
use Ushahidi\Modules\V5\Requests\LayerRequest;
use Ushahidi\Modules\V5\Models\Layer;
use Ushahidi\Core\Exception\NotFoundException;

class LayerController extends V5Controller
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
        $collection = $this->queryBus->handle(new FetchLayerByIdQuery($id));
        $this->authorize('show', $collection);
        return new LayerResource($collection);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return LayerCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Layer::class);
        $collections = $this->queryBus->handle(FetchLayerQuery::FromRequest($request));
        return new LayerCollection($collections);
    } //end index()


    /**
     * Create new Layer.
     *
     * @param LayerRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(LayerRequest $request)
    {
        $command = CreateLayerCommand::fromRequest($request);
        $new_layer = new Layer($command->getLayerEntity()->asArray());
        $this->authorize('store', $new_layer);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  Layer.
     *
     * @param int id
     * @param LayerRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, LayerRequest $request)
    {
        $old_layer = $this->queryBus->handle(new FetchLayerByIdQuery($id));
        $command = UpdateLayerCommand::fromRequest($id, $request, $old_layer);
        $new_layer = new Layer($command->getLayerEntity()->asArray());
        $this->authorize('update', $new_layer);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new Layer.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $layer = $this->queryBus->handle(new FetchLayerByIdQuery($id));
        } catch (NotFoundException $e) {
            $layer = new Layer();
        }
        $this->authorize('delete', $layer);
        $this->commandBus->handle(new DeleteLayerCommand($id));
        return $this->deleteResponse($id);
    }// end delete
} //end class
