<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Contact\Contact;
use Ushahidi\Modules\V5\Repository\Contact\ContactRepository;
use Ushahidi\Modules\V5\Actions\Contact\Commands\UpdateContactCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\ContactLock as Lock;

class UpdateContactCommandHandler extends AbstractCommandHandler
{
    private $contact_repository;

    public function __construct(ContactRepository $contact_repository)
    {
        $this->contact_repository = $contact_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdateContactCommand) {
            throw new \Exception('Provided $command is not instance of UpdateContactCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateContactCommand $action
         */
        $this->isSupported($action);

        // $this->categoryRepository->update(
        //     $action->getCategoryId(),
        //     $action->getParentId(),
        //     $action->getTag(),
        //     $action->getSlug(),
        //     $action->getType(),
        //     $action->getDescription(),
        //     $action->getColor(),
        //     $action->getIcon(),
        //     $action->getPriority(),
        //     $action->getRole(),
        //     $action->getDefaultLanguage(),
        //     $action->getAvailableLanguages()
        // );

        // return $this->categoryRepository
        //     ->findById($action->getCategoryId());
        //return new Contact();
         $this->updateContact($action);
    }


    private function updateContact(UpdateContactCommand $action)
    {
        if (!$this->validateLockState($action->getId())) {
          //  return self::make422(Lock::getContactLockedErrorMessage($id));
            $this->failedValidation(Lock::getContactLockedErrorMessage($action->getId()));
        }
        DB::beginTransaction();
        try {
            // to do call from repo
          //  $contact = Contact::create($action->getContactEntity()->asArray());
            $contact  = Contact::find($action->getId());
            $contact->fill($action->getContactEntity()->asArray())->save();

            if (count($action->getCompletedStages())) {
                $this->saveContactStages($contact, $action->getCompletedStages());
            }

            $errors = $this->saveContactValues($contact, $action->getContactContent(), $contact->id);
            if (!empty($errors)) {
                DB::rollback();
                $this->failedValidation($errors);
            }
            //$this->updateTranslations(new Contact(), $contact->toArray(), $action->getTranslations(), $contact->id, 'contact');

            $errors = $this->updateTranslations(
                $contact,
                $contact->toArray(),
                $action->getTranslations() ?? [],
                $contact->id,
                'contact'
            );
            if (!empty($errors)) {
                DB::rollback();
               // return self::make422($errors, 'translation');
                 $this->failedValidation($errors);
            }
            Lock::releaseLock($action->getId());
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

    protected function validateLockState($contact_id)
    {
        if (Lock::contactIsLocked($contact_id)) {
            return false;
        }
        return true;
    }
}
