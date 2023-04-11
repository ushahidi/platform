<?php

namespace Ushahidi\Modules\V5\Models;

class SetPost extends BaseModel
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
    protected $table = 'posts_sets';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'set_id'
        ];
}//end class
