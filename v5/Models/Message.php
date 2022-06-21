<?php

namespace v5\Models;

class Message extends BaseModel
{
    public static $relationships = [
        'contact'
    ];

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
    protected $table = 'messages';

    /**
     * Add relations to eager load
     *
     * @var string[]
     */
    protected $with = ['contact'];
    protected $contact;

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
        'parent_id',
        'contact_id',
        'post_id',
        'data_source',
        'data_source_message_id',
        'title',
        'message',
        'datetime',
        'type',
        'status',
        'direction',
        'created',
        'additional_data',
        'notification_post_id'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
