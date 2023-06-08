<?php

namespace Ushahidi\Modules\V5\Actions\Contact\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\Contact as ContactEntity;
use Ushahidi\Modules\V5\Models\Stage;

class CreateContactCommand implements Command
{
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

    
    // todo: At some point we might want to change it into a parameter
    const DEFAULT_LANUGAGE = 'en';
    private $availableLanguages;

    public function __construct(
        ContactEntity $contact_entity,
        array $completed_stages,
        array $contact_content,
        array $translations
    ) {
        $this->contact_entity = $contact_entity;
        $this->completed_stages = $completed_stages;
        $this->contact_content = $contact_content;
        $this->translations = $translations;
    }

    public static function createFromRequest(ContactRequest $request): self
    {
        $user = Auth::user();
        $input['slug'] = Contact::makeSlug($request->input('slug') ?? $request->input('title'));
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['author_email'] = $request->input('author_email') ?? ($user ? $user->email : null);
        $input['author_realname'] = $request->input('author_realname') ??($user ? $user->realname : null);
        $input['form_id'] = $request->input('form_id');
        $input['parent_id'] = $request->input('parent_id');
        $input['type'] = $request->input('type');
        $input['title'] = $request->input('title');
        $input['content'] = $request->input('content');
        $input['status'] = $request->input('status') ?? ContactEntity::DEFAULT_STATUS;
        $input['contact_date'] = $request->input('contact_date');
        $input['locale'] = $request->input('locale') ?? ContactEntity::DEFAULT_LOCAL;
        $input['base_language'] = $request->input('base_language') ?? ContactEntity::DEFAULT_LOCAL;
        $input['published_to'] = $request->input('published_to');
        $input['created'] = time();
        $input['update'] = null;

        return new self(
            new ContactEntity($input),
            $request->input('completed_stages')??[],
            $request->input('contact_content')??[],
            $request->input('translations')??[],
        );
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
