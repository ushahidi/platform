<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Ohanzee\Entities\Contact as ContactEntity;
use Ushahidi\Modules\V5\Models\Stage;

class CreateContactCommand implements Command
{
    /**
     * @var ContactEntity
     */
    private $contact_entity;




    public function __construct(ContactEntity $contact_entity)
    {
        $this->contact_entity = $contact_entity;
    }

    public static function fromRequest(ContactRequest $request): self
    {

        $user = Auth::user();
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['data_source'] = $request->input('data_source');
        $input['type'] = $request->input('type');
        $input['contact'] = $request->input('contact');
        $input['can_notify'] = $request->input('can_notify');
        $input['created'] = time();
        $input['updated'] = null;

        return new self(new ContactEntity($input));
    }

    /**
     * @return ContactEntity
     */
    public function getContactEntity(): ContactEntity
    {
        return $this->contact_entity;
    }
}
