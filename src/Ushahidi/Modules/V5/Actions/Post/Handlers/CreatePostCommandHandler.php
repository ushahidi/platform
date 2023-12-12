<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Post\Commands\CreatePostCommand;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Actions\Post\Handlers\AbstractPostCommandHandler;

class CreatePostCommandHandler extends AbstractPostCommandHandler
{
    private $post_repository;

    public function __construct(PostRepository $post_repository)
    {
        $this->post_repository = $post_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof CreatePostCommand) {
            throw new \Exception('Provided $command is not instance of CreatePostCommand');
        }
    }

    /**
     * @param CreatePostCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action): int
    {
        $this->isSupported($action);
        return $this->createPost($action);
    }

    private function createPost(CreatePostCommand $action)
    {
        DB::beginTransaction();
        try {
            // to do call from repo
            $data = $action->getPostEntity()->asArray();
            $post = Post::create($data);

            if (count($action->getCompletedStages())) {
                $this->savePostStages($post, $action->getCompletedStages());
            }

            // Attempt auto-publishing post on creation
            if ($post->tryAutoPublish()) {
                $post->save();
            }

            $errors = $this->savePostValues($post, $action->getPostContent(), $post->id);
            if (!empty($errors)) {
                DB::rollback();
                $this->failedValidation($errors);
            }
            $errors = $this->saveTranslations(
                $post,
                $post->toArray(),
                $action->getTranslations() ?? [],
                $post->id,
                'post'
            );
            if (!empty($errors)) {
                DB::rollback();
               // return self::make422($errors, 'translation');
                return $this->failedValidation($errors);
            }
            DB::commit();
            // note: done after commit to avoid deadlock in the db
            // see comment in bulkPatchOperation() below
            return $post->id;
        } catch (\Exception $e) {
            DB::rollback();
           // dd($e);
            throw $e;
            //return self::make500($e->getMessage());
        }
    }
}
