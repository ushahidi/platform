<?php


namespace v5\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'survey' => 'v5\Models\Survey',
            'task' => 'v5\Models\Stage',
            'field' => 'v5\Models\Attribute',
            'category' => 'v5\Models\Category',
            'post' => 'v5\Models\Post\Post',
            'post_value_varchar' => 'v5\Models\PostValues\PostVarchar',
            'post_value_text' => 'v5\Models\PostValues\PostText',
            'post_value_datetime' => 'v5\Models\PostValues\PostDatetime',
            'post_value_decimal' => 'v5\Models\PostValues\PostDecimal',
            'post_value_geometry' => 'v5\Models\PostValues\PostGeometry',
            'post_value_int' => 'v5\Models\PostValues\PostInt',
            'post_value_markdown' => 'v5\Models\PostValues\PostMarkdown',
            'post_value_media' => 'v5\Models\PostValues\PostMedia',
            'post_value_point' => 'v5\Models\PostValues\PostPoint',
            'post_value_relation' => 'v5\Models\PostValues\PostRelation',
            'post_value_posts_media' => 'v5\Models\PostValues\PostsMedia',
            'post_value_posts_set' => 'v5\Models\PostValues\PostsSet',
            'post_value_posts_tag' => 'v5\Models\PostValues\PostTag'
        ]);
    }
}
