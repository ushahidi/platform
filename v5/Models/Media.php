<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Core\Entity\Permission;
use Illuminate\Support\Facades\Input;

class Media extends BaseModel
{
    public $errors;
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
    protected $table = 'media';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created',
        'updated'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'mime',
        'caption',
        'o_filename',
        'o_size',
        'o_width',
        'o_height'
    ];

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
//    public static function validationMessages()
//    {
//        return [
//        ];
//    }//end translations()

    /**
     * Return all validation rules
     * @return array
     */
    public function getRules()
    {
        return [
             'user_id' => 'nullable|sometimes|exists:users,id',
             'caption'  => [
                'string',
                'regex:/^[\pL\pN\pP ]++$/uD',
             ],
             'mime' => [
                 'required',
                    function ($attribute, $value, $fail) {
                        $allowed_mime_types = [
                         'image/gif', 'image/jpg', 'image/jpeg', 'image/png'
                        ];
                        if (!$value) {
                            return $fail(trans('validation.mime_not_empty'));
                        } elseif (!in_array($value, $allowed_mime_types)) {
                            return $fail(trans('validation.mime_type_not_allowed'));
                        }
                    }
             ],

             'o_filename' => [
                 'required'
             ],
             'o_size' => [
                 'required',
                    function ($attribute, $value, $fail) {
                        if ($value <= 0 || $value > $this->max_bytes) {
                            $size_in_mb = ($this->max_bytes / 1024) / 1024;
                            return $fail(trans('validation.size_error', ['o_size' => $size_in_mb]));
                        }
                    }
             ],
             'o_width' => [
                 'numeric'
             ],
             'o_height' => [
                 'numeric'
             ]
        ];
    }//end validationMessages()

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        return $query;
    }

    public function errors()
    {
        return $this->errors;
    }

    /**
     * Returns the attributes that can be translated for this model
     * @return string[]
     */
    public static function translatableAttributes():array
    {
        return [
            'caption'
        ];
    }
}//end class
