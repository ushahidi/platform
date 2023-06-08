<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\Requests\ContactRequest;
use Ushahidi\Core\Entity\Contact as ContactEntity;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @var int[]
     */
    private $completed_stages;

    /**
     * @var array
     * Stage[]
     */
    private $contact_content;
    private $translations;
    public function __construct(
        int $id,
        ContactEntity $contact_entity,
        array $completed_stages,
        array $contact_content,
        array $translations
    ) {
        $this->id = $id;
        $this->contact_entity = $contact_entity;
        $this->completed_stages = $completed_stages;
        $this->contact_content = $contact_content;
        $this->translations = $translations;
    }

    public static function fromRequest(int $id, ContactRequest $request, Contact $current_contact): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->input('user_id') ?? $current_contact->user_id;
        } else {
            $input['user_id'] = $current_contact->user_id;
        }

        $input['slug'] = $request->input('slug') ? Contact::makeSlug($request->input('slug')) : $current_contact->slug;
        $input['author_email'] = $request->input('author_email') ?? $current_contact->author_email;
        $input['author_realname'] = $request->input('author_realname') ?? $current_contact->author_realname;
        $input['form_id'] = $request->input('form_id') ?? $current_contact->form_id;
        $input['parent_id'] = $request->input('parent_id') ?? $current_contact->parent_id;
        $input['type'] = $request->input('type') ?? $current_contact->type;
        $input['title'] = $request->input('title') ?? $current_contact->title;
        $input['content'] = $request->input('content') ?? $current_contact->content;
        $input['status'] = $request->input('status') ?? $current_contact->status;
        $input['contact_date'] = $request->input('contact_date') ?? $current_contact->contact_date;
        $input['locale'] = $request->input('locale') ?? $current_contact->locale;
        $input['base_language'] = $request->input('base_language') ?? $current_contact->base_language;
        $input['published_to'] = $request->input('published_to') ?? $current_contact->published_to;
        $input['created'] = $current_contact->created;
        $input['update'] = time();


        return new self(
            $id,
            new ContactEntity($input),
            $request->input('completed_stages') ?? [],
            $request->input('contact_content') ?? [],
            $request->input('translations') ?? [],
        );
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

    /**
     * @return array
     */
    public function getCompletedStages(): array
    {
        return $this->completed_stages;
    }

    /**
     * @return array
     */
    public function getContactContent(): array
    {
        return $this->contact_content;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }
}
