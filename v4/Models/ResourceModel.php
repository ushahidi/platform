<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class ResourceModel
 * Base class for models that are exposed as HTTP resources
 * @package v4\Models
 */
class ResourceModel extends Model
{
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
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count() +1 ;

        // if other slugs exist that are the same, append the count to the slug
        $value = $count ? "{$slug}-{$count}" : $slug;

        return $value;
    }
}
