<?php

namespace Ushahidi\Modules\V5\Models\Concerns;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait HasSlug
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

        // since it's required for the models, make sure we've got some sort of slug
        // (fallback if Str::slug fails to parse language)
        if (!$slug) {
            # "generated-" prefix, followed by three seven random character groups joined by hyphens
            $slug = "generated-" . implode(
                '-',
                array_map(function () {
                    return Str::random(7);
                }, [1,2,3])
            );
        }

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
}
