<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Apikey\Queries\FetchApikeyByIdQuery;
use Ushahidi\Modules\V5\Actions\Apikey\Queries\FetchApikeyQuery;
use Ushahidi\Modules\V5\Http\Resources\Apikey\ApikeyResource;
use Ushahidi\Modules\V5\Http\Resources\Apikey\ApikeyCollection;
use Ushahidi\Modules\V5\Actions\Apikey\Commands\CreateApikeyCommand;
use Ushahidi\Modules\V5\Actions\Apikey\Commands\UpdateApikeyCommand;
use Ushahidi\Modules\V5\Actions\Apikey\Commands\DeleteApikeyCommand;
use Ushahidi\Modules\V5\Requests\ApiKeyRequest;
use Ushahidi\Modules\V5\Models\Apikey;
use Ushahidi\Core\Exception\NotFoundException;

class ApiKeysController extends V5Controller
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
        $apikey = $this->queryBus->handle(new FetchApikeyByIdQuery($id));
        $this->authorize('show', $apikey);
        return new ApikeyResource($apikey);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return ApikeyCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Apikey::class);
        $apikeys = $this->queryBus->handle(FetchApikeyQuery::FromRequest($request));
        return new ApikeyCollection($apikeys);
    } //end index()


    /**
     * Create new Apikey.
     *
     * @param ApiKeyRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ApiKeyRequest $request)
    {
        $command = CreateApikeyCommand::fromRequest($request);
        $new_apikey = new Apikey($command->getApikeyEntity()->asArray());
        $this->authorize('store', $new_apikey);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  Apikey.
     *
     * @param int id
     * @param ApiKeyRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, ApiKeyRequest $request)
    {
        $old_apikey = $this->queryBus->handle(new FetchApikeyByIdQuery($id));
        $command = UpdateApikeyCommand::fromRequest($id, $request, $old_apikey);
        $new_apikey = new Apikey($command->getApikeyEntity()->asArray());
        $this->authorize('update', $new_apikey);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new Apikey.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $apikey = $this->queryBus->handle(new FetchApikeyByIdQuery($id));
        } catch (NotFoundException $e) {
            $apikey = new Apikey();
        }
        $this->authorize('delete', $apikey);
        $this->commandBus->handle(new DeleteApikeyCommand($id));
        return $this->deleteResponse($id);
    }// end delete
} //end class
