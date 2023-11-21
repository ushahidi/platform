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
     * @var bool
     */
    private $has_completed_stages;

    /**
     * @var array
     * Stage[]
     */
    private $post_content;
    private $translations;
    public function __construct(
        int $id,
        PostEntity $post_entity,
        ?array $completed_stages,
        ?array $post_content,
        ?array $translations
    ) {
        $this->id = $id;
        $this->post_entity = $post_entity;
        $this->completed_stages = $completed_stages;
        $this->post_content = $post_content;
        $this->translations = $translations;
    }

    public static function fromRequest(int $id, PostRequest $request, Post $current_post): self
    {
        // $jsonData = json_decode($request->getContent(), true);
        //dd($jsonData);
        //  dd($request->input());

        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->has('user_id')
                ? $request->input('user_id') : $current_post->user_id;
            ;
        } else {
            $input['user_id'] = $current_post->user_id;
        }

        $input['slug'] = $request->input('slug') ? Post::makeSlug($request->input('slug')) : $current_post->slug;
        $input['author_email'] = $request->has('author_email')
            ? $request->input('author_email') : $current_post->author_email;
        $input['author_realname'] = $request->has('author_realname')
            ? $request->input('author_realname') : $current_post->author_realname;
        $input['form_id'] = $request->has('form_id')
            ? $request->input('form_id') : $current_post->form_id;
        $input['parent_id'] = $request->has('parent_id')
            ? $request->input('parent_id') : $current_post->parent_id;
        $input['type'] = $request->has('type')
            ? $request->input('type') : $current_post->type;
        $input['title'] = $request->has('title')
            ? $request->input('title') : $current_post->title;
        $input['content'] = $request->has('content')
            ? $request->input('content') : $current_post->content;
        $input['status'] = $request->has('status')
            ? $request->input('status') : $current_post->status;
        $input['post_date'] = $request->has('post_date')
            ? $request->input('post_date') : $current_post->post_date;
        $input['locale'] = $request->has('locale')
            ? $request->input('locale') : $current_post->locale;
        $input['base_language'] = $request->has('base_language')
            ? $request->input('base_language') : $current_post->base_language;
        $input['published_to'] = $request->has('published_to')
            ? $request->input('published_to') : $current_post->published_to;
        $input['created'] = self::ensureTimestamp($current_post->created);
        $input['updated'] = time();

        if ($request->has('completed_stages')) {
            $completed_stages = $request->input('completed_stages') ?? [];
        } else {
            $completed_stages = null;
        }

        if ($request->has('translations')) {
            $translations = $request->input('translations') ?? [];
        } else {
            $translations = null;
        }

        if ($request->has('post_content')) {
            $post_content = $request->input('post_content') ?? [];
        } else {
            $post_content = null;
        }

        return new self(
            $id,
            new PostEntity($input),
            $completed_stages,
            $post_content,
            $translations,
        );
    }

    private static function ensureTimestamp($var)
    {
        // Check if it's an integer
        if (is_int($var)) {
            // Check if it's a valid Unix timestamp
            $date = \DateTime::createFromFormat('U', $var);
            if ($date && $date->getTimestamp() == $var) {
                return $var;
            }
        }

        // Assuming it's a date string and converting it to a timestamp
        $timestamp = strtotime($var);

        // Check if the conversion was successful
        if ($timestamp === false) {
            // Handle the error according to your needs
            throw new \Exception("Invalid date or timestamp: $var");
        }

        return $timestamp;
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
    public function getCompletedStages(): ?array
    {
        return $this->completed_stages;
    }

    /**
     * @return array
     */
    public function getPostContent(): ?array
    {
        return $this->post_content;
    }

    /**
     * @return array
     */
    public function getTranslations(): ?array
    {
        return $this->translations;
    }
}
