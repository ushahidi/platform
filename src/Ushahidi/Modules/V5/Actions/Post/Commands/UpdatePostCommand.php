<?php

namespace Ushahidi\Modules\V5\Actions\Post\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Requests\PostRequest;
use Ushahidi\Core\Entity\Post as PostEntity;
use Illuminate\Support\Facades\Auth;

class UpdatePostCommand implements Command
{

    /**
     * @var int
     */
    private $id;

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
    public function __construct(
        int $id,
        PostEntity $post_entity,
        array $completed_stages,
        array $post_content,
        array $translations
    ) {
        $this->id = $id;
        $this->post_entity = $post_entity;
        $this->completed_stages = $completed_stages;
        $this->post_content = $post_content;
        $this->translations = $translations;
    }

    public static function fromRequest(int $id, PostRequest $request, Post $current_post): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->input('user_id') ?? $current_post->user_id;
        } else {
            $input['user_id'] = $current_post->user_id;
        }

        $input['slug'] = $request->input('slug') ? Post::makeSlug($request->input('slug')) : $current_post->slug;
        $input['author_email'] = $request->input('author_email') ?? $current_post->author_email;
        $input['author_realname'] = $request->input('author_realname') ?? $current_post->author_realname;
        $input['form_id'] = $request->input('form_id') ?? $current_post->form_id;
        $input['parent_id'] = $request->input('parent_id') ?? $current_post->parent_id;
        $input['type'] = $request->input('type') ?? $current_post->type;
        $input['title'] = $request->input('title') ?? $current_post->title;
        $input['content'] = $request->input('content') ?? $current_post->content;
        $input['status'] = $request->input('status') ?? $current_post->status;
        $input['post_date'] = $request->input('post_date') ?? $current_post->post_date;
        $input['locale'] = $request->input('locale') ?? $current_post->locale;
        $input['base_language'] = $request->input('base_language') ?? $current_post->base_language;
        $input['published_to'] = $request->input('published_to') ?? $current_post->published_to;
        $input['created'] = strtotime($current_post->created);
        $input['updated'] = time();


        return new self(
            $id,
            new PostEntity($input),
            $request->input('completed_stages') ?? [],
            $request->input('post_content') ?? [],
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
