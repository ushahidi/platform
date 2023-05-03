<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Query\Query;
use App\Bus\Query\AbstractQueryHandler;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Modules\V5\Models\Post\Post;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Http\Resources\PostValueCollection;
use Ushahidi\Modules\V5\Http\Resources\ContactPointerResource;
use Ushahidi\Modules\V5\Http\Resources\MessagePointerResource;
use Ushahidi\Modules\V5\Http\Resources\LockCollection;
use Ushahidi\Modules\V5\Http\Resources\Survey\TaskCollection;

class FindPostByIdQueryHandler extends AbstractQueryHandler
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof FindPostByIdQuery) {
            throw new \InvalidArgumentException('Provided action is not supported');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var FindPostByIdQuery $action
         */
        $this->isSupported($action);

        $post = $this->postRepository->findById(
            $action->getId(),
            array_unique(array_merge($action->getFields(), $action->getFieldsForRelationship())),
            $action->getWithRelationship()
        );
        $post = $this->addHydrateRelationships($post, $action->getHydrates());
        $post = $this->hideFieldsUsedByRelationships(
            $post,
            array_diff($action->getFieldsForRelationship(), $action->getFields())
        );
        return $this->hideUnwantedRelationships($post, $action->getHydrates());
    }

    private function addHydrateRelationships(Post $post, array $hydrates)
    {
        foreach ($hydrates as $hydrate) {
            switch ($hydrate) {
                case 'categories':
                    // $result['categories'] = $post->categories;
                    break;
                case 'completed_stages':
                    $post->completed_stages = $post->postStages;

                    break;
                case 'post_content':
                    $post->post_content = $this->getResourcePostContent($post);
                    break;
                case 'translations':
                    break;
                case 'contact':
                    $post->contact = null;
                    if ($post->message) {
                        //$post->contact = new ContactPointerResource($post->message->contact);
                        $post->contact = $post->message->contact;
                    }
                    break;
                case 'message':
                    // if ($post->message) {
                    //     $post->message = new MessagePointerResource($post->message);
                    // }
                    break;
                case 'locks':
                    $post->locks = new LockCollection($post->locks);
                    break;

                case 'source':
                    $message = $post->message;
                    $post->source = $message && isset($message->type)
                        ? $message->type
                        : Post::DEFAULT_SOURCE_TYPE;

                    break;

                case 'data_source_message_id':
                    $post->data_source_message_id = null;
                    $message = $post->message;
                    if ($message) {
                        $post->data_source_message_id = $message->data_source_message_id ?? null;
                    }
                    break;
                case 'enabled_languages':
                    $post->enabled_languages = [
                        'default' => $post->base_language,
                        'available' => $post->translations->groupBy('language')->keys()
                    ];
                    $relations['enabled_languages'] = true;
                    break;
            }
        }
        return $post;
    }
    private function hideFieldsUsedByRelationships(Post $post, array $fields = [])
    {
        foreach ($fields as $field) {
            $post->offsetUnset($field);
        }
        $post->offsetUnset('values_int');

        return $post;
    }
    private function hideUnwantedRelationships(Post $post, array $hydrates)
    {
        // hide  post_content relationships
        $post->makeHidden('survey');
        $post->makeHidden('valuesVarchar');
        $post->makeHidden('valuesInt');
        $post->makeHidden('valuesText');
        $post->makeHidden('valuesDatetime');
        $post->makeHidden('valuesDecimal');
        $post->makeHidden('valuesDecimal');
        $post->makeHidden('valuesGeometry');
        $post->makeHidden('valuesMarkdown');
        $post->makeHidden('valuesMedia');
        $post->makeHidden('valuesPoint');
        $post->makeHidden('valuesRelation');
        $post->makeHidden('valuesPostsMedia');
        $post->makeHidden('valuesPostsSet');
        $post->makeHidden('valuesPostTag');

        // hide source relationships
        if (!in_array('message', $hydrates)) {
             $post->makeHidden('message');
        }

        // hide completed_stages relationships
        $post->makeHidden('postStages');


        return $post;
    }

    private function getResourcePostContent($post)
    {
        $values = $post->getPostValues(); // Calling method on Post Model
        $no_values = $values->count() === 0 ? true : false;
        $col = new Collection([
            'values' => $values,
            'tasks' => $post->survey ? $post->survey->tasks : []
        ]);
        if ($no_values && $post->survey) {
            $post_content = new TaskCollection($post->survey->tasks);
        } elseif ($post->survey) {
            $post_content = new PostValueCollection($col);
        } else {
            $post_content = Collection::make([]);
        }
        return $post_content;
    }
}
