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
use Ushahidi\Modules\V5\Requests\ContactRequest;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Core\Exception\NotFoundException;

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
        $contact = $this->queryBus->handle(new FetchContactByIdQuery($id));
        $this->authorize('show', $contact);
        return new ContactResource($contact);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return ContactCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Contact::class);
        $contacts = $this->queryBus->handle(FetchContactQuery::FromRequest($request));
        return new ContactCollection($contacts);
    } //end index()


    /**
     * Create new Contact.
     *
     * @param ContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ContactRequest $request)
    {
        $command = CreateContactCommand::fromRequest($request);
        $new_contact = new Contact($command->getContactEntity()->asArray());
        $this->authorize('store', $new_contact);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  Contact.
     *
     * @param int id
     * @param ContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, ContactRequest $request)
    {
        $old_contact = $this->queryBus->handle(new FetchContactByIdQuery($id));
        $command = UpdateContactCommand::fromRequest($id, $request, $old_contact);
        $new_contact = new Contact($command->getContactEntity()->asArray());
        $this->authorize('update', $new_contact);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new Contact.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $contact = $this->queryBus->handle(new FetchContactByIdQuery($id));
        } catch (NotFoundException $e) {
            $contact = new Contact();
        }
        $this->authorize('delete', $contact);
        $this->commandBus->handle(new DeleteContactCommand($id));
        return $this->deleteResponse($id);
    }// end delete
} //end class
