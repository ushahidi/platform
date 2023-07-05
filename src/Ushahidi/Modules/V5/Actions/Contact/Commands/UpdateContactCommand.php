<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Requests\ContactRequest;
use Ushahidi\Core\Entity\Contact as ContactEntity;
use Ushahidi\Core\Ohanzee\Entities\Contact as OhanzeeContact;
use Ushahidi\Modules\V5\Models\Contact as EloquentContact;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateContactCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var ContactEntity
     */
    private $contact_entity;

    public function __construct(
        int $id,
        ContactEntity $contact_entity
    ) {
        $this->id = $id;
        $this->contact_entity = $contact_entity;
    }

    public static function fromRequest(int $id, ContactRequest $request, EloquentContact $current_contact): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->input('user_id') ?? $current_contact->user_id;
        } else {
            $input['user_id'] = $current_contact->user_id;
        }

        $input['data_source'] = $request->input('data_source') ?? $current_contact->data_source;
        $input['type'] = $request->input('type')?$request->input('type'):$current_contact->type;
        $input['contact'] = $request->input('contact') ?? $current_contact->contact;
        $input['can_notify'] = ParameterUtilities::checkIfEmpty($request->input('can_notify'))
            ? $request->input('can_notify')
            : $current_contact->can_notify;
        $input['created'] = strtotime($current_contact->created);
        $input['updated'] = time();

        return new self($id, new OhanzeeContact($input));
    }
    private static function hasPermissionToUpdateUser($user)
    {
        if ($user->role === "admin") {
            return true;
        }
        return false;
    }

    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return ContactEntity
     */
    public function getContactEntity(): ContactEntity
    {
        return $this->contact_entity;
    }
}
