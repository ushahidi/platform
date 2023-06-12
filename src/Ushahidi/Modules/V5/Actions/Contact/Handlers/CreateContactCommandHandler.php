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
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        return $this->contact_repository->create($action->getContactEntity());
    }
}
