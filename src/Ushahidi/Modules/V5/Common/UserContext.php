<?php

namespace Ushahidi\Modules\V5\Common;

use App\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
//use Ushahidi\Core\Entity\User;
//use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Modules\V5\Models\User;




trait UserContext
{

    protected $overrideUserId = false;
    protected $cachedUser = false;

    public function getUser()
    {
        // If user override is set
        if ($this->overrideUserId) {
            // Use that
            $userId = $this->overrideUserId;
        } else {
            // Using the OAuth resource server, get the userid (owner id) for this request
            $genericUser = Auth::guard()->user();

            $userId = $genericUser ? $genericUser->id : null;
        }
        //dd($userId);

        // If we have no user id return
        if (! $userId) {
            // return an empty user
            //return $this->userRepo->getEntity();
            return new User();
        }

        // If we haven't already loaded the user, or the user has changed
        if (! $this->cachedUser || $this->cachedUser->getId() !== $userId) {
            // Using the user repository, load the user
            //$this->cachedUser = $this->userRepo->get($userId);
            $this->cachedUser = User::find($userId);
        }

        return $this->cachedUser;
    }

    public function getGenericUser(){
        $genericUser = Auth::guard()->user();
        return $genericUser;
    }

    
}
