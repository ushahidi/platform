<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Media\Queries\FetchMediaByIdQuery;
use Ushahidi\Modules\V5\Http\Resources\Media\MediaResource;
use Ushahidi\Modules\V5\Actions\Media\Commands\CreateMediaCommand;
use Ushahidi\Modules\V5\Actions\Media\Commands\UpdateMediaCommand;
use Ushahidi\Modules\V5\Actions\Media\Commands\DeleteMediaCommand;
use Ushahidi\Modules\V5\Requests\MediaRequest;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Core\Exception\NotFoundException;

class MediaController extends V5Controller
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
        $media = $this->queryBus->handle(new FetchMediaByIdQuery($id));
        $this->authorizeAnyone('show', $media);
        return new MediaResource($media);
    } //end show()



    // /**
    //  * Display the specified resource.
    //  *
    //  * @return MediaCollection
    //  * @throws \Illuminate\Auth\Access\AuthorizationException
    //  */
    // public function index(Request $request)
    // {
    //     $this->authorizeAnyone('index', Media::class);
    //     $medias = $this->queryBus->handle(FetchMediaQuery::FromRequest($request));
    //     return new MediaCollection($medias);
    // } //end index()


    /**
     * Create new Media.
     *
     * @param MediaRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(MediaRequest $request)
    {
        $command = CreateMediaCommand::fromRequest($request);
        $new_media = new Media($command->getMediaEntity()->asArray());
        $this->authorizeAnyone('store', $new_media);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  Media.
     *
     * @param int id
     * @param MediaRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, MediaRequest $request)
    {
        $old_media = $this->queryBus->handle(new FetchMediaByIdQuery($id));
        $command = UpdateMediaCommand::fromRequest($id, $request, $old_media);
        $new_media = new Media($command->getMediaEntity()->asArray());
        $this->authorize('update', $new_media);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new Media.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $media = $this->queryBus->handle(new FetchMediaByIdQuery($id));
        } catch (NotFoundException $e) {
            $media = new Media();
        }
        $this->authorize('delete', $media);
        $this->commandBus->handle(new DeleteMediaCommand($id));
        return $this->deleteResponse($id);
    }// end delete
} //end class
