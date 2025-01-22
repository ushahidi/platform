<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Webhook\Queries\FetchWebhookByIdQuery;
use Ushahidi\Modules\V5\Actions\Webhook\Queries\FetchWebhookQuery;
use Ushahidi\Modules\V5\Http\Resources\Webhook\WebhookResource;
use Ushahidi\Modules\V5\Http\Resources\Webhook\WebhookCollection;
use Ushahidi\Modules\V5\Actions\Webhook\Commands\CreateWebhookCommand;
use Ushahidi\Modules\V5\Actions\Webhook\Commands\UpdateWebhookCommand;
use Ushahidi\Modules\V5\Actions\Webhook\Commands\DeleteWebhookCommand;
use Ushahidi\Modules\V5\Requests\WebhookRequest;
use Ushahidi\Modules\V5\Models\Webhook\Webhook;
use Ushahidi\Core\Exception\NotFoundException;

class WebhookController extends V5Controller
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
        $webhook = $this->queryBus->handle(new FetchWebhookByIdQuery($id));
        $this->authorize('show', $webhook);
        return new WebhookResource($webhook);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return WebhookCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Webhook::class);
        $webhooks = $this->queryBus->handle(FetchWebhookQuery::FromRequest($request));
        return new WebhookCollection($webhooks);
    } //end index()


    /**
     * Create new Webhook.
     *
     * @param WebhookRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(WebhookRequest $request)
    {
        $command = CreateWebhookCommand::fromRequest($request);
        $new_webhook = new Webhook($command->getWebhookEntity()->asArray());
        $this->authorize('store', $new_webhook);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  Webhook.
     *
     * @param int id
     * @param WebhookRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, WebhookRequest $request)
    {
        $old_webhook = $this->queryBus->handle(new FetchWebhookByIdQuery($id));
        $command = UpdateWebhookCommand::fromRequest($id, $request, $old_webhook);
        $new_webhook = new Webhook($command->getWebhookEntity()->asArray());
        $this->authorize('update', $new_webhook);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new Webhook.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $webhook = $this->queryBus->handle(new FetchWebhookByIdQuery($id));
        } catch (NotFoundException $e) {
            $webhook = new Webhook();
        }
        $this->authorize('delete', $webhook);
        $this->commandBus->handle(new DeleteWebhookCommand($id));
        return $this->deleteResponse($id);
    }// end delete

    public function updatePosts(int $id, WebhookRequest $request)
    {
        $old_webhook = $this->queryBus->handle(new FetchWebhookByIdQuery($id));
        $command = UpdateWebhookCommand::fromRequest($id, $request, $old_webhook);
        $new_webhook = new Webhook($command->getWebhookEntity()->asArray());
        $this->authorize('update', $new_webhook);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update
} //end class
