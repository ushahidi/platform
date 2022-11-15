<?php
namespace Ushahidi\Modules\V5\Providers;

use Ushahidi\Modules\V5\Models;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class MorphServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'survey' => Models\Survey::class,
            'task' => Models\Stage::class,
            'field' => Models\Attribute::class,
            'category' => Models\Category::class,
            'post' => Models\Post\Post::class,
            'post_value_varchar' => Models\PostValues\PostVarchar::class,
            'post_value_text' => Models\PostValues\PostText::class,
            'post_value_datetime' => Models\PostValues\PostDatetime::class,
            'post_value_decimal' => Models\PostValues\PostDecimal::class,
            'post_value_geometry' => Models\PostValues\PostGeometry::class,
            'post_value_int' => Models\PostValues\PostInt::class,
            'post_value_markdown' => Models\PostValues\PostMarkdown::class,
            'post_value_media' => Models\PostValues\PostMedia::class,
            'post_value_point' => Models\PostValues\PostPoint::class,
            'post_value_relation' => Models\PostValues\PostRelation::class,
            'post_value_posts_media' => Models\PostValues\PostsMedia::class,
            'post_value_posts_set' => Models\PostValues\PostsSet::class,
            'post_value_posts_tag' => Models\PostValues\PostTag::class,
        ]);
    }
}
