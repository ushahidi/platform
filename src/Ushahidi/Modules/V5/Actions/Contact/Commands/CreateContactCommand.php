<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\Contact as ContactEntity;
use Ushahidi\Modules\V5\Models\Stage;
use Ushahidi\Contracts\Sources;
use Ushahidi\Contracts\Contact as ContractContact;

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

    public static function forWhatsapp($user_id, String $contact_number, String $type = "phone", $can_notify = 0): self
    {

        $user = Auth::user();
        $input['user_id'] =$user_id ?? ($user ? $user->id : null);
        $input['data_source'] = Sources::WHATSAPP;
        $input['type'] = $type ?? ContractContact::PHONE;
        $input['contact'] = $contact_number;
        $input['can_notify'] = $can_notify ?? 0;
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
