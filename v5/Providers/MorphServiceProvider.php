<?php


namespace v4\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'survey' => 'v4\Models\Survey',
            'task' => 'v4\Models\Stage',
            'field' => 'v4\Models\Attribute',
            'category' => 'v4\Models\Category',
            'post' => 'v4\Models\Post',
            'post_value_varchar' => 'v4\Models\PostValues\PostVarchar',
            'post_value_text' => 'v4\Models\PostValues\PostText',
            'post_value_datetime' => 'v4\Models\PostValues\PostDatetime',
            'post_value_decimal' => 'v4\Models\PostValues\PostDecimal',
            'post_value_geometry' => 'v4\Models\PostValues\PostGeometry',
            'post_value_int' => 'v4\Models\PostValues\PostInt',
            'post_value_markdown' => 'v4\Models\PostValues\PostMarkdown',
            'post_value_media' => 'v4\Models\PostValues\PostMedia',
            'post_value_point' => 'v4\Models\PostValues\PostPoint',
            'post_value_relation' => 'v4\Models\PostValues\PostRelation',
            'post_value_posts_media' => 'v4\Models\PostValues\PostsMedia',
            'post_value_posts_set' => 'v4\Models\PostValues\PostsSet',
            'post_value_posts_tag' => 'v4\Models\PostValues\PostTag'
        ]);
    }
}
