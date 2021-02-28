<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

/**
 * Class ResourceModel
 * Base class for models that are exposed as HTTP resources
 * @package v5\Models
 */
class BaseModel extends Model
{

    protected $validationRules = [];

    /**
     * Get the model's slug
     *
     * @param  string  $value
     * @return void
     */
    public function getSlugAttribute($value)
    {
        return $value;
    }
    /**
     * Set the model's slug format
     *
     * @param  string  $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        if (isset($value) && (!isset($this->attributes['slug']))) {
            $value = self::makeSlug($value);
            $this->attributes['slug'] = $value;
        }
    }

    public static function makeSlug($value)
    {
        // produce a slug based on the value
        $slug = Str::slug($value);

        // check to see if any other slugs exist that are the same & count them
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        // if other slugs exist that are the same, append the count to the slug
        $value = $count ? "{$slug}-{$count}" : $slug;

        return $value;
    }

    /**
     * @param $time timestamp
     */
    protected static function makeDate($time)
    {
        if (is_numeric($time) && !!$time) {
            $d = new \DateTime();
            $d->setTimestamp($time);
            return $d->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        }
        return $time;
    }
    public function validationMessages()
    {
        return [];
    }
    public function getRules()
    {
        return [];
    }

    public function validate($data = [])
    {
        $input = array_merge($this->attributes, $data);
        $v = Validator::make($input, $this->getRules($data), $this->validationMessages());
        // check for failure
        if (!$v->fails()) {
            return true;
        }
        // set errors and return false
        $this->errors = $v->errors();
        return false;
    }
    /**
     * Attempt to validate input, if successful fill this object
     * @param array $inputArray associative array of values for this object to validate against and fill this object
     * @throws ValidationException if validation fails. Used for displaying errors in view
     */
    public function validateAndFill($inputArray)
    {
        // must validate input before injecting into model
        $this->validate($inputArray);
        $this->fill($inputArray);
    }
}
