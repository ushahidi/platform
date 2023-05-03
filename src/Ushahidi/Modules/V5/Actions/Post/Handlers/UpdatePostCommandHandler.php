<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Modules\V5\Actions\Post\Commands\UpdatePostCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Actions\Post\Handlers\AbstractPostCommandHandler;
use Ushahidi\Modules\V5\Models\Lock;

class UpdatePostCommandHandler extends AbstractPostCommandHandler
{
    private $post_repository;

    public function __construct(PostRepository $post_repository)
    {
        $this->post_repository = $post_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdatePostCommand) {
            throw new \Exception('Provided $command is not instance of UpdatePostCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdatePostCommand $action
         */
        $this->isSupported($action);

        // $this->categoryRepository->update(
        //     $action->getCategoryId(),
        //     $action->getParentId(),
        //     $action->getTag(),
        //     $action->getSlug(),
        //     $action->getType(),
        //     $action->getDescription(),
        //     $action->getColor(),
        //     $action->getIcon(),
        //     $action->getPriority(),
        //     $action->getRole(),
        //     $action->getDefaultLanguage(),
        //     $action->getAvailableLanguages()
        // );

        // return $this->categoryRepository
        //     ->findById($action->getCategoryId());
        //return new Post();
         $this->updatePost($action);
    }


    private function updatePost(UpdatePostCommand $action)
    {
        if (!$this->validateLockState($action->getId())) {
          //  return self::make422(Lock::getPostLockedErrorMessage($id));
            $this->failedValidation(Lock::getPostLockedErrorMessage($action->getId()));
        }
        DB::beginTransaction();
        try {
            // to do call from repo
          //  $post = Post::create($action->getPostEntity()->asArray());
            $post  = Post::find($action->getId());
            $post->fill($action->getPostEntity()->asArray())->save();

            if (count($action->getCompletedStages())) {
                $this->savePostStages($post, $action->getCompletedStages());
            }

            $errors = $this->savePostValues($post, $action->getPostContent(), $post->id);
            if (!empty($errors)) {
                DB::rollback();
                $this->failedValidation($errors);
            }
            //$this->updateTranslations(new Post(), $post->toArray(), $action->getTranslations(), $post->id, 'post');

            $errors = $this->updateTranslations(
                $post,
                $post->toArray(),
                $action->getTranslations() ?? [],
                $post->id,
                'post'
            );
            if (!empty($errors)) {
                DB::rollback();
               // return self::make422($errors, 'translation');
                 $this->failedValidation($errors);
            }
            Lock::releaseLock($action->getId());
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

    protected function validateLockState($post_id)
    {
        if (Lock::postIsLocked($post_id)) {
            return false;
        }
        return true;
    }
}
