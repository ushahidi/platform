<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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

    /**
     * Convenience method for getting the model's table name statically
     */
    public static function getTableName()
    {
        return (new static)->getTable();
    }

    public static function makeSlug($value)
    {
        // TBD: this function gets called *twice* when a post is created
        //      figure out why

        // produce a slug based on the value
        // (note this gives us a regex-safe string)
        $slug = Str::slug($value);

        // allowing there may already be several slugs with the same prefix, i.e.:
        //    popular-title, popular-title-1, .... , popular-title-99
        // query for the last one of them
        $last = DB::table(static::getTableName())
            ->select(['slug'])
            ->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")
            ->orderByRaw('LENGTH(slug) DESC')
            ->orderBy('slug', 'DESC')
            ->limit(1)
            ->first();

        // no matches results in a last_count of null, otherwise we numerically
        // interpret anything after "${slug}-" (0 if empty)
        $last_count = null;
        if ($last) {
            $last_n = substr($last->slug, strlen($slug)+1);
            if (strlen($last_n) > 0) {
                $last_count = intval($last_n);
            }
        }
        // just the $slug if no similar slug found is null, start/keep counting otherwise
        $value = $last ? "{$slug}-" . ($last_count+1) : $slug;

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
