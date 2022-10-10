<?php
namespace Ushahidi\Modules\V5\Common;

use App\Auth\GenericUser as user;
use Illuminate\Database\Eloquent\Model;

trait OwnerAccess
{
    /**
     * Check if $user is the owner of $ownable
     *
     * @return boolean
     */
    protected function isUserOwner(Model $ownable, User $user)
    {
        // @todo ensure we always check the original user_id not the updated value!
        return ($user->id && $ownable->user_id === $user->id);
    }

    /**
     * Check if $user and owner of $ownable are anonymous (user id 0)
     *
     * @return boolean
     */
    protected function isUserAndOwnerAnonymous(Model $ownable, User $user)
    {
        // @todo ensure we always check the original user_id not the updated value!
        return (! $user->id && ! $ownable->user_id);
    }
}
