<?php

namespace Ushahidi\Modules\V5\Providers;

use Ushahidi\Modules\V5\Models;
use Ushahidi\Modules\V5\Policies;
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
        Models\User::class => Policies\UserPolicy::class,
        Models\UserSetting::class => Policies\UserSettingPolicy::class,
        Models\CountryCode::class => Policies\CountryCodePolicy::class,
        Models\Permissions::class => Policies\PermissionsPolicy::class,
        Models\Role::class => Policies\RolePolicy::class,
        Models\Post\Post::class => Policies\PostPolicy::class,
        Models\Tos::class => Policies\TosPolicy::class,
        Models\Set::class => Policies\SetPolicy::class,
        Models\SetPost::class => Policies\CollectionPostPolicy::class,
        Models\Config::class => Policies\ConfigPolicy::class,
        Models\Contact::class => Policies\ContactPolicy::class,
        Models\Message::class => Policies\MessagePolicy::class,
        Models\Notification::class => Policies\NotificationPolicy::class,
        Models\Layer::class => Policies\LayerPolicy::class,
        Models\CSV::class => Policies\CSVPolicy::class,
        Models\ExportJob::class => Policies\ExportJobPolicy::class,
        Models\Media::class => Policies\MediaPolicy::class,
        Models\Apikey::class => Policies\APIKeyPolicy::class,
        Models\Webhooks\Webhook::class => Policies\WebhookPolicy::class
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
