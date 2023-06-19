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
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Http\Resources\Post\PostResource ;

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
        $message = $this->queryBus->handle(new FetchMessageByIdQuery($id));
        $this->authorize('show', $message);
        return new MessageResource($message);
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
        $messages = $this->queryBus->handle(FetchMessageQuery::FromRequest($request));
        return new MessageCollection($messages);
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
       
        $request = $this->retrieveDatafromParent($request);
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
        //$this->authorize('update', $new_message);
        $this->authorize('update', $old_message);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showPost(int $id)
    {
        $message = $this->queryBus->handle(new FetchMessageByIdQuery($id));
        $this->authorize('show', $message);
        if ($message->post_id) {
            $post = $this->queryBus->handle(new FindPostByIdQuery($message->post_id));
            return new postResource($post);
        }
        throw new NotFoundException("Post does not exist for this message");
    } //end show()


    private function retrieveDatafromParent($request)
    {
        if ($request->input('parent_id')) {
            try {
                $parent = $this->show($request->input('parent_id'));
            } catch (NotFoundException $e) {
                $parent = null;
            }
            if ($parent) {
                $request->merge(['type' => $parent->type]);
                $request->merge(['data_source' => $parent->data_source]);
            }
          //  $input['type'] = $request->input('type');
          //  $input['data_source'] = $request->input('data_source');

     //       $parent = $this->repo->get($this->payload['parent_id']);
      //      $entity->setState(['type' => $parent->type,
      //                         'data_source' => $parent->data_source]);
        }
        return $request;
    }
} //end class
