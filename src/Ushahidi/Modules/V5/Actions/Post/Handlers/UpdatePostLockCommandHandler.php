<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Command\Command;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Post\Commands\UpdatePostLockCommand;
use Ushahidi\Modules\V5\Repository\Post\PostLockRepository;
use Ushahidi\Core\Exception\NotFoundException;

class UpdatePostLockCommandHandler extends V5CommandHandler
{
    private $post_lock_repository;
    private $current_post_lock;

    public function __construct(PostLockRepository $post_lock_repository)
    {
        $this->post_lock_repository = $post_lock_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof UpdatePostLockCommand) {
            throw new \Exception('Provided command is not of type ' . UpdatePostLockCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdatePostLockCommand $action
         */
        // To do : make it as transaction
        $this->isSupported($action);
        $this->updatePostLock($action->getPostId());
    }

    private function updatePostLock($post_id)
    {
        // if there is no lock create new one
        // if user can break the lock "has admin role" then release the old lock if it from other user
        // If the lock is inactive and the lock is not for current user release old one and create new one

        $this->setCurrentPostLock($post_id);

        if ($this->current_post_lock) {
            if (($this->lockIsBreakable()) || (!$this->currentLockIsActive() && !$this->userOwnsCurrentLock())) {
                $this->deleteLock($post_id);
                $this->createNewLock($post_id);
                return;
            }
        } else {
            $this->createNewLock($post_id);
            return;
        }
    }
    public function lockIsBreakable()
    {
        $user = Auth::user();
        return $user->role === "admin";
    }
    private function currentLockIsActive()
    {
        // Check if the lock has expired
        // Locks are active for a maximum of 5 minutes
        if (time() - $this->current_post_lock->expire > 0) {
            return false;
        }
        return true;
    }

    private function userOwnsCurrentLock()
    {
        $user = Auth::user();
        return intval($user->id) === intval($this->current_post_lock->user_id);
    }
    private function setCurrentPostLock($post_id): void
    {
        try {
            $this->current_post_lock = $this->post_lock_repository->findByPostId($post_id);
        } catch (NotFoundException $e) {
            $this->current_post_lock = null;
        }
    }
    private function deleteLock($post_id)
    {
        $this->post_lock_repository->findByPostId($post_id);
    }
    private function createNewLock($post_id)
    {
        $expires = strtotime("+5 minutes");
        $user = Auth::user();
        $lock = ['user_id' => $user->id, 'post_id' => $post_id, 'expires' => $expires];
        return $this->post_lock_repository->create($lock);
    }
}
