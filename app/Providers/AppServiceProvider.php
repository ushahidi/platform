<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ushahidi\Core\Tool\SiteManager;
use Ushahidi\Core\Tool\FeatureManager;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Modules\V5\Models\Post\Post as PostModel;
use Ushahidi\Modules\V5\Repository\Category\CategoryRepository;
use Ushahidi\Modules\V5\Repository\Category\EloquentCategoryRepository;
use Ushahidi\Modules\V5\Repository\Translation\EloquentTranslationRepository;
use Ushahidi\Modules\V5\Repository\Translation\TranslationRepository;
use Ushahidi\Modules\V5\Repository\CountryCode\CountryCodeRepository;
use Ushahidi\Modules\V5\Repository\CountryCode\EloquentCountryCodeRepository;
use Ushahidi\Modules\V5\Repository\Post\EloquentPostRepository;
use Ushahidi\Modules\V5\Repository\Post\PostRepository as V5PostRepository;
use Ushahidi\Modules\V5\Repository\Permissions\PermissionsRepository;
use Ushahidi\Modules\V5\Repository\Permissions\EloquentPermissionsRepository;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;
use Ushahidi\Modules\V5\Repository\Role\EloquentRoleRepository;
use Ushahidi\Modules\V5\Repository\Tos\TosRepository;
use Ushahidi\Modules\V5\Repository\Tos\EloquentTosRepository;
use Ushahidi\Modules\V5\Repository\User;
use Ushahidi\Modules\V5\Repository\Survey;
use Ushahidi\Modules\V5\Repository\Set;
use Ushahidi\Modules\V5\Repository\Post;
use Ushahidi\Modules\V5\Repository\Config;
use Ushahidi\Modules\V5\Repository\Contact;
use Ushahidi\Modules\V5\Repository\Message;
use Ushahidi\Modules\V5\Repository\Notification;
use Ushahidi\Modules\V5\Repository\Layer;
use Ushahidi\Modules\V5\Repository\CSV;
use Ushahidi\Modules\V5\Repository\Export;
use Ushahidi\Modules\V5\Repository\Media;
use Ushahidi\Modules\V5\Repository\Apikey;
use Ushahidi\Modules\V5\Repository\Webhook;
use Ushahidi\Modules\V5\Repository\HXL;
use Jenssegers\Agent\Agent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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

        // Register OhanzeeResolver
        $this->app->singleton(OhanzeeResolver::class, function ($app) {
            return new OhanzeeResolver();
        });

        $this->registerFeatures();
    }

    public function registerFeatures()
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
        $this->app->bind(V5PostRepository::class, function ($app) {
            return new EloquentPostRepository(PostModel::query());
        });
        $this->app->bind(Survey\SurveyRepository::class, Survey\EloquentSurveyRepository::class);
        $this->app->bind(Survey\TaskRepository::class, Survey\EloquentTaskRepository::class);
        $this->app->bind(Survey\SurveyRoleRepository::class, Survey\EloquentSurveyRoleRepository::class);
        $this->app->bind(Survey\SurveyStatesRepository::class, Survey\EloquentSurveyStatesRepository::class);
        $this->app->bind(Set\SetRepository::class, Set\EloquentSetRepository::class);
        $this->app->bind(Set\SetPostRepository::class, Set\EloquentSetPostRepository::class);
        $this->app->bind(Post\PostLockRepository::class, Post\EloquentPostLockRepository::class);
        $this->app->bind(Config\ConfigRepository::class, Config\EloquentConfigRepository::class);
        $this->app->bind(Contact\ContactRepository::class, Contact\EloquentContactRepository::class);
        $this->app->bind(Message\MessageRepository::class, Message\EloquentMessageRepository::class);
        $this->app->bind(
            Notification\NotificationRepository::class,
            Notification\EloquentNotificationRepository::class
        );
        $this->app->bind(Layer\LayerRepository::class, Layer\EloquentLayerRepository::class);
        $this->app->bind(CSV\CSVRepository::class, CSV\EloquentCSVRepository::class);
        $this->app->bind(Export\ExportJobRepository::class, Export\EloquentExportJobRepository::class);
        $this->app->bind(Media\MediaRepository::class, Media\EloquentMediaRepository::class);
        $this->app->bind(Apikey\ApikeyRepository::class, Apikey\EloquentApikeyRepository::class);
        $this->app->bind(Webhook\WebhookRepository::class, Webhook\EloquentWebhookRepository::class);
        $this->app->bind(HXL\HXLRepository::class, HXL\EloquentHXLRepository::class);
    }
}
