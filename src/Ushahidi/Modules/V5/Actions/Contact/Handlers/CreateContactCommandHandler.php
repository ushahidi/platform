<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Contact\Commands\CreateContactCommand;
use Ushahidi\Modules\V5\Repository\Contact\ContactRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Contact;

class CreateContactCommandHandler extends AbstractCommandHandler
{
    private $contact_repository;

    public function __construct(ContactRepository $contact_repository)
    {
        $this->contact_repository = $contact_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreateContactCommand) {
            throw new \Exception('Provided $command is not instance of CreateContactCommand');
        }
    }

    /**
     * @param CreateContactCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action): int
    {
        $this->isSupported($action);
        return $this->createContact($action);
    }

    private function createContact(CreateContactCommand $action)
    {
        DB::beginTransaction();
        try {
            // to do call from repo
            $contact = Contact::create($action->getContactEntity()->asArray());

            if (count($action->getCompletedStages())) {
                $this->saveContactStages($contact, $action->getCompletedStages());
            }

            // Attempt auto-publishing contact on creation
            if ($contact->tryAutoPublish()) {
                $contact->save();
            }

            $errors = $this->saveContactValues($contact, $action->getContactContent(), $contact->id);
            if (!empty($errors)) {
                DB::rollback();
                $this->failedValidation($errors);
            }
            $errors = $this->saveTranslations(
                $contact,
                $contact->toArray(),
                $action->getTranslations() ?? [],
                $contact->id,
                'contact'
            );
            if (!empty($errors)) {
                DB::rollback();
               // return self::make422($errors, 'translation');
                return $this->failedValidation($errors);
            }
            DB::commit();
            // note: done after commit to avoid deadlock in the db
            // see comment in bulkPatchOperation() below
            return $contact->id;
        } catch (\Exception $e) {
            DB::rollback();
           // dd($e);
            throw $e;
            //return self::make500($e->getMessage());
        }
    }
}
