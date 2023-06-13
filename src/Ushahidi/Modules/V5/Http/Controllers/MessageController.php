<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Message\Queries\FetchMessageByIdQuery;
use Ushahidi\Modules\V5\Actions\Message\Queries\FetchMessageQuery;
use Ushahidi\Modules\V5\Http\Resources\Message\MessageResource;
use Ushahidi\Modules\V5\Http\Resources\Message\MessageCollection;
use Ushahidi\Modules\V5\Actions\Message\Commands\CreateMessageCommand;
use Ushahidi\Modules\V5\Actions\Message\Commands\UpdateMessageCommand;
use Ushahidi\Modules\V5\Actions\Message\Commands\DeleteMessageCommand;
use Ushahidi\Modules\V5\Requests\MessageRequest;
use Ushahidi\Modules\V5\Models\Message;
use Ushahidi\Core\Exception\NotFoundException;

class MessageController extends V5Controller
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
        $collection = $this->queryBus->handle(new FetchMessageByIdQuery($id));
        $this->authorize('show', $collection);
        return new MessageResource($collection);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return MessageCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Message::class);
        $collections = $this->queryBus->handle(FetchMessageQuery::FromRequest($request));
        return new MessageCollection($collections);
    } //end index()


    /**
     * Create new Message.
     *
     * @param MessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(MessageRequest $request)
    {
        $command = CreateMessageCommand::fromRequest($request);
        $new_message = new Message($command->getMessageEntity()->asArray());
        $this->authorize('store', $new_message);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  Message.
     *
     * @param int id
     * @param MessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, MessageRequest $request)
    {
        $old_message = $this->queryBus->handle(new FetchMessageByIdQuery($id));
        $command = UpdateMessageCommand::fromRequest($id, $request, $old_message);
        $new_message = new Message($command->getMessageEntity()->asArray());
        $this->authorize('update', $new_message);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new Message.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $message = $this->queryBus->handle(new FetchMessageByIdQuery($id));
        } catch (NotFoundException $e) {
            $message = new Message();
        }
        $this->authorize('delete', $message);
        $this->commandBus->handle(new DeleteMessageCommand($id));
        return $this->deleteResponse($id);
    }// end delete
} //end class
