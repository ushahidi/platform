<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserResettoken extends BaseModel
{
    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Specify the table to load with Survey
     *
     * @var string
     */
    protected $table = 'user_reset_tokens';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'reset_token',
        'created'
        ];
}//end class
