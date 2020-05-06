<?php


namespace v4\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphServiceProvider extends ServiceProvider
{
    public function boot() {
        Relation::morphMap([
            'survey' => 'v4\Models\Survey',
            'stage' => 'v4\Models\Stage',
            'attribute' => 'v4\Models\Attribute',
        ]);
    }
}
