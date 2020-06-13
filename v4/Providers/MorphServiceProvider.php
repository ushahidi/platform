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
            'post' => 'v4\Models\Post'
        ]);
    }
}
