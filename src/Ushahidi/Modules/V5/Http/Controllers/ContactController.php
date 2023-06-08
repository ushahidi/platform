<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Contact\Queries\FetchContactByIdQuery;
use Ushahidi\Modules\V5\Actions\Contact\Queries\FetchContactQuery;
use Ushahidi\Modules\V5\Http\Resources\Contact\ContactResource;
use Ushahidi\Modules\V5\Http\Resources\Contact\ContactCollection;
use Ushahidi\Modules\V5\Actions\Contact\Commands\CreateContactCommand;
use Ushahidi\Modules\V5\Actions\Contact\Commands\UpdateContactCommand;
use Ushahidi\Modules\V5\Actions\Contact\Commands\DeleteContactCommand;
use Ushahidi\Modules\V5\DTO\ContactSearchFields;
use Ushahidi\Core\Entity\Contact as ContactEntity;
use Ushahidi\Modules\V5\Requests\ContactRequest;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\Policies\ContactPolicy;

class ContactController extends V5Controller
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
        $collection = $this->queryBus->handle(new FetchContactByIdQuery($id));
        return new ContactResource($collection);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return ContactCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $collections = $this->queryBus->handle(FetchContactQuery::FromRequest($request));
        return new ContactCollection($collections);
    } //end index()


    /**
     * Create new Contact.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ContactRequest $request)
    {
        $this->authorize('store', Contact::class);
        return $this->show(
            $this->commandBus->handle(
                new CreateContactCommand(
                    ContactEntity::buildEntity($request->input())
                )
            )
        );
    } //end store()

    public function update(int $id, ContactRequest $request)
    {
        $saved_search = $this->queryBus->handle(new FetchContactByIdQuery($id));
        $this->authorize('update', $saved_search);
        $this->commandBus->handle(
            new UpdateContactCommand(
                $id,
                ContactEntity::buildEntity($request->input(), 'update', $saved_search->toArray())
            )
        );
        return $this->show($id);
    }

    public function delete(int $id)
    {
        $collection = $this->queryBus->handle(new FetchContactByIdQuery($id));
        $this->authorize('delete', $collection);
        $this->commandBus->handle(new DeleteContactCommand($id));
        return response()->json(['result' => ['deleted' => $id]]);
    }
} //end class
