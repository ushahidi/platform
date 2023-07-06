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

        return $this->contact_repository->update($action->getId(), $action->getContactEntity());
    }
}
