<?php

namespace Ushahidi\Modules\V5\Actions\Post\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\Post as PostEntity;
use Ushahidi\Modules\V5\Models\Stage;

class CreatePostCommand implements Command
{
    /**
     * @var PostEntity
     */
    private $post_entity;

    /**
     * @var int[]
     */
    private $completed_stages;

    /**
     * @var array
     * Stage[]
     */
    private $post_content;
    private $translations;

    
    // todo: At some point we might want to change it into a parameter
    const DEFAULT_LANUGAGE = 'en';
    private $availableLanguages;

    public function __construct(
        PostEntity $post_entity,
        array $completed_stages,
        array $post_content,
        array $translations
    ) {
        $this->post_entity = $post_entity;
        $this->completed_stages = $completed_stages;
        $this->post_content = $post_content;
        $this->translations = $translations;
    }

    public static function createFromRequest(PostRequest $request): self
    {
        $user = Auth::user();
        $input['slug'] = Post::makeSlug($request->input('slug') ?? $request->input('title'));
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['author_email'] = $request->input('author_email') ?? ($user ? $user->email : null);
        $input['author_realname'] = $request->input('author_realname') ??($user ? $user->realname : null);
        $input['form_id'] = $request->input('form_id');
        $input['parent_id'] = $request->input('parent_id');
        $input['type'] = $request->input('type');
        $input['title'] = $request->input('title');
        $input['content'] = $request->input('content');
        $input['status'] = $request->input('status') ?? PostEntity::DEFAULT_STATUS;
        $input['post_date'] = $request->input('post_date');
        $input['locale'] = $request->input('locale') ?? PostEntity::DEFAULT_LOCAL;
        $input['base_language'] = $request->input('base_language') ?? PostEntity::DEFAULT_LOCAL;
        $input['published_to'] = $request->input('published_to');
        $input['created'] = time();
        $input['update'] = null;

        return new self(
            new PostEntity($input),
            $request->input('completed_stages')??[],
            $request->input('post_content')??[],
            $request->input('translations')??[],
        );
    }

    /**
     * @return PostEntity
     */
    public function getPostEntity(): PostEntity
    {
        return $this->post_entity;
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
    public function getPostContent(): array
    {
        return $this->post_content;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }
}
