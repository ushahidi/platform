<?php

namespace Ushahidi\App\V5\Providers;

use Ushahidi\App\V5\Models;
use Ushahidi\App\V5\Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Models\Survey::class => Policies\SurveyPolicy::class,
        Models\Category::class => Policies\CategoryPolicy::class,
        Models\Post\Post::class => Policies\PostPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
