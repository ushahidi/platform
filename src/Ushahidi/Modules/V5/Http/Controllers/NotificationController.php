<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Notification\Queries\FetchNotificationByIdQuery;
use Ushahidi\Modules\V5\Actions\Notification\Queries\FetchNotificationQuery;
use Ushahidi\Modules\V5\Http\Resources\Notification\NotificationResource;
use Ushahidi\Modules\V5\Http\Resources\Notification\NotificationCollection;
use Ushahidi\Modules\V5\Actions\Notification\Commands\CreateNotificationCommand;
use Ushahidi\Modules\V5\Actions\Notification\Commands\UpdateNotificationCommand;
use Ushahidi\Modules\V5\Actions\Notification\Commands\DeleteNotificationCommand;
use Ushahidi\Modules\V5\Requests\NotificationRequest;
use Ushahidi\Modules\V5\Models\Notification;
use Ushahidi\Core\Exception\NotFoundException;

class NotificationController extends V5Controller
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
        $collection = $this->queryBus->handle(new FetchNotificationByIdQuery($id));
        $this->authorize('show', $collection);
        return new NotificationResource($collection);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return NotificationCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Notification::class);
        $collections = $this->queryBus->handle(FetchNotificationQuery::FromRequest($request));
        return new NotificationCollection($collections);
    } //end index()


    /**
     * Create new Notification.
     *
     * @param NotificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(NotificationRequest $request)
    {
        $command = CreateNotificationCommand::fromRequest($request);
        $new_notification = new Notification($command->getNotificationEntity()->asArray());
        $this->authorize('store', $new_notification);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  Notification.
     *
     * @param int id
     * @param NotificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, NotificationRequest $request)
    {
        $old_notification = $this->queryBus->handle(new FetchNotificationByIdQuery($id));
        $command = UpdateNotificationCommand::fromRequest($id, $request, $old_notification);
        $new_notification = new Notification($command->getNotificationEntity()->asArray());
        //$this->authorize('update', $new_notification);
        $this->authorize('update', $old_notification);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new Notification.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $notification = $this->queryBus->handle(new FetchNotificationByIdQuery($id));
        } catch (NotFoundException $e) {
            $notification = new Notification();
        }
        $this->authorize('delete', $notification);
        $this->commandBus->handle(new DeleteNotificationCommand($id));
        return $this->deleteResponse($id);
    }// end delete
} //end class
