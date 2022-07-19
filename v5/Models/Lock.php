<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Lock extends BaseModel
{
    /**
     * Specify the table
     *
     * @var string
     */
    protected $table = 'post_locks';

    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
      //  'expires',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'expires'
    ];



    public function post()
    {
        return $this->hasOne('v5\Models\Post\Post', 'id', 'post_id');
    }

    public function user()
    {
        return $this->hasOne('v5\Models\User', 'id', 'user_id');
    }

    /**
     * check if post has a nonexpired lock
     * @param $post_id int
     * @return bool
     */
    public static function hasActiveLock($post_id):bool
    {
        $lock = self::select("expires")->where("post_id", "=", $post_id)->first();
        if (!empty($lock)) {
            $curtime = time();
            // Check if the lock has expired
            if (($curtime - $lock->expires) > 0) {
               // $release = $this->releaseLockByPostId($post_id);
                return false;
            }
            return true;
        }
        return false;
    }


    /**
     * check if the post has a nonexpired lock form other user
     * @param $post_id int
     * @return bool
     */
    public static function postIsLocked($post_id):bool
    {
        $authorizer = service('authorizer.post');
        $user =$authorizer->getUser();

        $lock = self::where("post_id", "=", $post_id)->first();
        $curtime = time();
    
        if (empty($lock)) { // there is no lock
            return false;
        } elseif ($user->id === (int)$lock->user_id) { // current user is the owner of the lock
            return false;
        } elseif ($curtime > $lock->expires) { // other user has a lock but it expired
            return false;
        }
        return true;
    }

    /**
     * remove the lock of current user on a particular post
     * @param $post_id int
     * @return bool
     */
    public static function releaseLock($post_id):bool
    {
        $authorizer = service('authorizer.post');
        $user_id = $authorizer->getUser()->id;
        return( self::where([["post_id","=",$post_id],["user_id","=",$user_id]])->delete());
    }

     /**
     * remove the lock on a particular post
     * @param $post_id int
     * @return bool
     */
    public static function releaseLockByPostId($post_id):bool
    {
        return( self::where("post_id", "=", $post_id)->delete());
    }

     /**
     * remove the lock by id
     * @param $lock_id int
     * @return bool
     */
    public static function releaseLockByLockId($lock_id):bool
    {
        return( self::where("id", "=", $lock_id)->delete());
    }

     /**
     * remove all locks owned by a particular user
     * @param $user_id int
     * @return bool
     */
    public static function releaseLocksByUserId($user_id):bool
    {
        return( self::where("user_id", "=", $user_id)->delete());
    }

    
     /**
     * remove all locks owned by a particular user
     * @param $post_id int
     * @return array
     */
    public static function getPostLockedErrorMessage($post_id):array
    {
        $user_id = self::where("post_id", "=", $post_id)->first()->user_id;
        $owner = User::where('id', '=', $user_id)->first();
        $error = [""=>[trans('validation.post_locked', ['user' => $owner->realname])]];
        return $error;
    }
}//end class
