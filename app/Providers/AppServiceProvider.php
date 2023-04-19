<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ushahidi\Core\Config\SiteManager;
use Ushahidi\Core\Config\FeatureManager;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Addons\{
    Mteja\MtejaSource,
    AfricasTalking\AfricasTalkingSource
};
use Ushahidi\Modules\V5\Repository\Set;
use Ushahidi\Modules\V5\Repository\User;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Repository\Survey;
use Ushahidi\Modules\V5\Repository\Tos\TosRepository;
use Ushahidi\Modules\V5\Repository\Tos\EloquentTosRepository;
use Ushahidi\Modules\V5\Repository\Post\PostRepository;
use Ushahidi\Modules\V5\Repository\Post\EloquentPostRepository;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;
use Ushahidi\Modules\V5\Repository\Role\EloquentRoleRepository;
use Ushahidi\Modules\V5\Repository\CountryCode\CountryCodeRepository;
use Ushahidi\Modules\V5\Repository\CountryCode\EloquentCountryCodeRepository;
use Ushahidi\Modules\V5\Repository\Permissions\PermissionsRepository;
use Ushahidi\Modules\V5\Repository\Permissions\EloquentPermissionsRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * For now this configuration is temporary,
         * should be moved to an isolated place within the addon directory
         */
        $this->app['datasources']->extend('africastalking', AfricasTalkingSource::class);

        $this->app['datasources']->extend('mteja', MtejaSource::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register OhanzeeResolver
        $this->app->singleton(OhanzeeResolver::class, function ($app) {
            return new OhanzeeResolver();
        });

        $this->app->singleton('site', function ($app, $params) {
            return new SiteManager(
                $app[ConfigRepository::class],
                $params ? $params['cache_lifetime'] : null
            );
        });

        $this->app['events']->listen('site.changed', function ($site) {
            $this->app['site']->setDefault($site);
        });

        $this->app->bind('feature', function ($app) {
            return new FeatureManager($app[ConfigRepository::class]);
        });

        $this->registerRepositories();
    }

    public function registerRepositories()
    {
        $this->app->bind(CountryCodeRepository::class, EloquentCountryCodeRepository::class);

        $this->app->bind(
            User\UserRepository::class,
            User\EloquentUserRepository::class
        );
        $this->app->bind(
            user\UserSettingRepository::class,
            user\EloquentUserSettingRepository::class
        );
        $this->app->bind(PermissionsRepository::class, EloquentPermissionsRepository::class);
        $this->app->bind(RoleRepository::class, EloquentRoleRepository::class);
        $this->app->bind(TosRepository::class, EloquentTosRepository::class);
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(TranslationRepository::class, EloquentTranslationRepository::class);
        $this->app->bind(PostRepository::class, function ($app) {
            return new EloquentPostRepository(Post::query());
        });
        $this->app->bind(Survey\SurveyRepository::class, Survey\EloquentSurveyRepository::class);
        $this->app->bind(Survey\TaskRepository::class, Survey\EloquentTaskRepository::class);
        $this->app->bind(Survey\SurveyRoleRepository::class, Survey\EloquentSurveyRoleRepository::class);
        $this->app->bind(Survey\SurveyStatesRepository::class, Survey\EloquentSurveyStatesRepository::class);
        $this->app->bind(Set\SetRepository::class, Set\EloquentSetRepository::class);
        $this->app->bind(Set\SetPostRepository::class, Set\EloquentSetPostRepository::class);
    }
}
