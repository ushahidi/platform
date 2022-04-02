<?php

namespace v5\Models;

class Contact extends BaseModel
{
    public static $relationships = [
        'messages'
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

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
    protected $table = 'contacts';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var  array
     */
    protected $hidden = [
    ];

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'data_source',
        'type',
        'contact',
        'created',
        'updated',
        'can_notify'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
