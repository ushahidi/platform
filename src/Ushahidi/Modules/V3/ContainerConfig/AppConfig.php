<?php

namespace Ushahidi\Modules\V3\ContainerConfig;

use Ushahidi\Core;
use Ushahidi\Modules\V3;
use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class AppConfig extends ContainerConfig
{
    /**
     *
     * Define params, setters, and services before the Container is locked.
     *
     * @param Container $di The DI container.
     *
     */
    public function define(Container $di): void
    {
        // All services set in the container should follow a `prefix.name` format,
        // such as `repository.user` or `validate.user.login`.
        //

        // Validator mapping
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['apikeys'] = [
            'create' => $di->lazyNew(V3\Validator\ApiKey\Create::class),
            'update' => $di->lazyNew(V3\Validator\ApiKey\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['config'] = [
            'update' => $di->lazyNew(V3\Validator\Config\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['forms'] = [
            'create' => $di->lazyNew(V3\Validator\Form\Create::class),
            'update' => $di->lazyNew(V3\Validator\Form\Update::class),
            'delete' => $di->lazyNew(V3\Validator\Form\Delete::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['form_attributes'] = [
            'create' => $di->lazyNew(V3\Validator\Form\Attribute\Create::class),
            'update' => $di->lazyNew(V3\Validator\Form\Attribute\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['form_roles'] = [
            'create' => $di->lazyNew(V3\Validator\Form\Role\Create::class),
            'update_collection' => $di->lazyNew(V3\Validator\Form\Role\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['form_contacts'] = [
            'create' => $di->lazyNew(V3\Validator\Form\Contact\Create::class),
            'update' => $di->lazyNew(V3\Validator\Form\Contact\Update::class),
        ];

        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['form_stages'] = [
            'create' => $di->lazyNew(V3\Validator\Form\Stage\Create::class),
            'update' => $di->lazyNew(V3\Validator\Form\Stage\Update::class),
            'delete' => $di->lazyNew(V3\Validator\Form\Stage\Delete::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['layers'] = [
            'create' => $di->lazyNew(V3\Validator\Layer\Create::class),
            'update' => $di->lazyNew(V3\Validator\Layer\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['media'] = [
            'create' => $di->lazyNew(V3\Validator\Media\Create::class),
            'delete' => $di->lazyNew(V3\Validator\Media\Delete::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['posts'] = [
            'create' => $di->lazyNew(V3\Validator\Post\Create::class),
            'update' => $di->lazyNew(V3\Validator\Post\Update::class),
            'import' => $di->lazyNew(V3\Validator\Post\Import::class),
            'export' => $di->lazyNew(V3\Validator\Post\Export::class),
            'webhook-update' => $di->lazyNew(V3\Validator\Post\Create::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['export_jobs'] = [
            'create' => $di->lazyNew(V3\Validator\ExportJob\Create::class),
            'update' => $di->lazyNew(V3\Validator\ExportJob\Update::class),
        ];

        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['posts_lock'] = [
            'create' => $di->lazyNew(V3\Validator\Post\Create::class),
            'update' => $di->lazyNew(V3\Validator\Post\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['tags'] = [
            'create' => $di->lazyNew(V3\Validator\Tag\Create::class),
            'update' => $di->lazyNew(V3\Validator\Tag\Update::class),
            'delete' => $di->lazyNew(V3\Validator\Tag\Delete::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['users'] = [
            'create' => $di->lazyNew(V3\Validator\User\Create::class),
            'update' => $di->lazyNew(V3\Validator\User\Update::class),
            'register' => $di->lazyNew(V3\Validator\User\Register::class),
            'passwordreset' => $di->lazyNew(V3\Validator\User\Reset::class)
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['user_settings'] = [
            'create' => $di->lazyNew(V3\Validator\User\Setting\Create::class),
            'update' => $di->lazyNew(V3\Validator\User\Setting\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['messages'] = [
            'create' => $di->lazyNew(V3\Validator\Message\Create::class),
            'update' => $di->lazyNew(V3\Validator\Message\Update::class),
            'receive' => $di->lazyNew(V3\Validator\Message\Receive::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['savedsearches'] = [
            'create' => $di->lazyNew(V3\Validator\SavedSearch\Create::class),
            'update' => $di->lazyNew(V3\Validator\SavedSearch\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['sets'] = [
            'create' => $di->lazyNew(V3\Validator\Set\Create::class),
            'update' => $di->lazyNew(V3\Validator\Set\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['notifications'] = [
            'create' => $di->lazyNew(V3\Validator\Notification\Create::class),
            'update' => $di->lazyNew(V3\Validator\Notification\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['webhooks'] = [
            'create' => $di->lazyNew(V3\Validator\Webhook\Create::class),
            'update' => $di->lazyNew(V3\Validator\Webhook\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['contacts'] = [
            'create' => $di->lazyNew(V3\Validator\Contact\Create::class),
            'update' => $di->lazyNew(V3\Validator\Contact\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['sets_posts'] = [
            'create' => $di->lazyNew(V3\Validator\Set\Post\Create::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['csv'] = [
            'create' => $di->lazyNew(V3\Validator\CSV\Create::class),
            'update' => $di->lazyNew(V3\Validator\CSV\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['csv'] = [
            'create' => $di->lazyNew(V3\Validator\CSV\Create::class),
            'update' => $di->lazyNew(V3\Validator\CSV\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['roles'] = [
            'create' => $di->lazyNew(V3\Validator\Role\Create::class),
            'update' => $di->lazyNew(V3\Validator\Role\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['permissions'] = [
            'create' => $di->lazyNew(V3\Validator\Permission\Create::class),
            'update' => $di->lazyNew(V3\Validator\Permission\Update::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['tos'] = [
            'create' => $di->lazyNew(V3\Validator\Tos\Create::class),
        ];

        // Formatter mapping
        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['map'] = [
            'apikeys'              => $di->lazyNew(V3\Formatter\ApiKey::class),
            'config'               => $di->lazyNew(V3\Formatter\Config::class),
            'dataproviders'        => $di->lazyNew(V3\Formatter\Dataprovider::class),
            'country_codes'        => $di->lazyNew(V3\Formatter\CountryCode::class),
            'export_jobs'          => $di->lazyNew(V3\Formatter\ExportJob::class),
            'forms'                => $di->lazyNew(V3\Formatter\Form::class),
            'form_attributes'      => $di->lazyNew(V3\Formatter\Form\Attribute::class),
            'form_roles'           => $di->lazyNew(V3\Formatter\Form\Role::class),
            'form_stages'          => $di->lazyNew(V3\Formatter\Form\Stage::class),
            'form_contacts'        => $di->lazyNew(V3\Formatter\Form\Contact::class),
            'form_stats'           => $di->lazyNew(V3\Formatter\Form\Stats::class),
            'layers'               => $di->lazyNew(V3\Formatter\Layer::class),
            'media'                => $di->lazyNew(V3\Formatter\Media::class),
            'messages'             => $di->lazyNew(V3\Formatter\Message::class),
            'posts'                => $di->lazyNew(V3\Formatter\Post::class),
            'posts_lock'           => $di->lazyNew(V3\Formatter\Post\Lock::class),
            'tags'                 => $di->lazyNew(V3\Formatter\Tag::class),
            'savedsearches'        => $di->lazyNew(V3\Formatter\Set::class),
            'sets'                 => $di->lazyNew(V3\Formatter\Set::class),
            'sets_posts'           => $di->lazyNew(V3\Formatter\Post::class),
            'savedsearches_posts'  => $di->lazyNew(V3\Formatter\Post::class),
            'users'                => $di->lazyNew(V3\Formatter\User::class),
            'user_settings'        => $di->lazyNew(V3\Formatter\User\Setting::class),
            'notifications'        => $di->lazyNew(V3\Formatter\Notification::class),
            'webhooks'             => $di->lazyNew(V3\Formatter\Webhook::class),
            'contacts'             => $di->lazyNew(V3\Formatter\Contact::class),
            'csv'                  => $di->lazyNew(V3\Formatter\CSV::class),
            'roles'                => $di->lazyNew(V3\Formatter\Role::class),
            'permissions'          => $di->lazyNew(V3\Formatter\Permission::class),
            // Formatter for post exports. Defaults to CSV export
            'posts_export'         => $di->lazyNew(V3\Formatter\Post\CSV::class),
            'tos' => $di->lazyNew(V3\Formatter\Tos::class),
        ];

        // Formatter parameters
        $di->setters[V3\Formatter\ApiKey::class]['setAuth'] = $di->lazyGet("authorizer.apikey");
        $di->setters[V3\Formatter\Config::class]['setAuth'] = $di->lazyGet("authorizer.config");
        $di->setters[V3\Formatter\CSV::class]['setAuth'] = $di->lazyGet("authorizer.csv");
        $di->setters[V3\Formatter\Dataprovider::class]['setAuth'] = $di->lazyGet("authorizer.dataprovider");
        $di->setters[V3\Formatter\ExportJob::class]['setAuth'] = $di->lazyGet("authorizer.export_job");
        $di->setters[V3\Formatter\Form::class]['setAuth'] = $di->lazyGet("authorizer.form");
        $di->setters[V3\Formatter\Form\Attribute::class]['setAuth']
            = $di->lazyGet("authorizer.form_attribute");
        $di->setters[V3\Formatter\Form\Role::class]['setAuth'] = $di->lazyGet("authorizer.form_role");
        $di->setters[V3\Formatter\Form\Stage::class]['setAuth'] = $di->lazyGet("authorizer.form_stage");
        $di->setters[V3\Formatter\Layer::class]['setAuth'] = $di->lazyGet("authorizer.layer");
        $di->setters[V3\Formatter\Media::class]['setAuth'] = $di->lazyGet("authorizer.media");
        $di->setters[V3\Formatter\Message::class]['setAuth'] = $di->lazyGet("authorizer.message");
        $di->setters[V3\Formatter\Post::class]['setAuth'] = $di->lazyGet("authorizer.post");
        $di->setters[V3\Formatter\Post\Lock::class]['setAuth'] = $di->lazyGet("authorizer.post");
        $di->setters[V3\Formatter\Tag::class]['setAuth'] = $di->lazyGet("authorizer.tag");
        $di->setters[V3\Formatter\Tos::class]['setAuth'] = $di->lazyGet("authorizer.tos");
        $di->setters[V3\Formatter\User::class]['setAuth'] = $di->lazyGet("authorizer.user");
        $di->setters[V3\Formatter\User\Setting::class]['setAuth'] = $di->lazyGet("authorizer.user_setting");
        $di->setters[V3\Formatter\Savedsearch::class]['setAuth'] = $di->lazyGet("authorizer.savedsearch");
        $di->setters[V3\Formatter\Set::class]['setAuth'] = $di->lazyGet("authorizer.set");
        $di->setters[V3\Formatter\Set\Post::class]['setAuth'] = $di->lazyGet("authorizer.set_post");
        $di->setters[V3\Formatter\Notification::class]['setAuth'] = $di->lazyGet("authorizer.notification");
        $di->setters[V3\Formatter\Webhook::class]['setAuth'] = $di->lazyGet("authorizer.webhook");
        $di->setters[V3\Formatter\Contact::class]['setAuth'] = $di->lazyGet("authorizer.contact");
        $di->setters[V3\Formatter\Role::class]['setAuth'] = $di->lazyGet("authorizer.role");
        $di->setters[V3\Formatter\Permission::class]['setAuth'] = $di->lazyGet("authorizer.permission");
        $di->setters[V3\Formatter\Form\Stats::class]['setAuth'] = $di->lazyGet("authorizer.form_stats");
        $di->setters[V3\Formatter\CountryCode::class]['setAuth'] = $di->lazyGet("authorizer.country_code");


        // Set Formatter factory
        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['factory']
            = $di->newFactory(V3\Formatter\Collection::class);


        $di->set('tool.jsontranscode', $di->lazyNew(\Ushahidi\Core\Tool\JsonTranscode::class));

        // Formatters
        $di->set('formatter.entity.api', $di->lazyNew(V3\Formatter\API::class));
        $di->set('formatter.entity.country_code', $di->lazyNew(V3\Formatter\CountryCode::class));
        $di->set('formatter.entity.console', $di->lazyNew(V3\Formatter\Console::class));
        $di->set('formatter.entity.form.contact', $di->lazyNew(V3\Formatter\Form\Contact::class));
        $di->set('formatter.entity.form.stats', $di->lazyNew(V3\Formatter\Form\Stats::class));
        $di->set(
            'formatter.entity.form.contactcollection',
            $di->lazyNew(V3\Formatter\Form\ContactCollection::class)
        );
        $di->set('formatter.entity.post.value', $di->lazyNew(V3\Formatter\PostValue::class));
        $di->set('formatter.entity.post.lock', $di->lazyNew(V3\Formatter\Post\Lock::class));
        $di->set('formatter.entity.post.geojson', $di->lazyNew(V3\Formatter\Post\GeoJSON::class));
        $di->set(
            'formatter.entity.post.geojsoncollection',
            $di->lazyNew(V3\Formatter\Post\GeoJSONCollection::class)
        );
        $di->set('formatter.entity.post.stats', $di->lazyNew(V3\Formatter\Post\Stats::class));
        $di->set('formatter.entity.post.csv', $di->lazyNew(V3\Formatter\Post\CSV::class));

        $di->set('formatter.output.json', $di->lazyNew(V3\Formatter\JSON::class));
        $di->set('formatter.output.jsonp', $di->lazyNew(V3\Formatter\JSONP::class));

        // Formatter parameters
        $di->setters[V3\Formatter\JSONP::class]['setCallback'] = function () {
            return Request::current()->query('callback');
        };
        $di->params[V3\Formatter\Post::class] = [
            'value_formatter' => $di->lazyGet('formatter.entity.post.value')
        ];
        $di->setters[V3\Formatter\Post\GeoJSON::class]['setDecoder']
            = $di->lazyNew('Symm\Gisconverter\Decoders\WKT');
        $di->setters[V3\Formatter\Post\GeoJSONCollection::class]['setDecoder'] =
            $di->lazyNew('Symm\Gisconverter\Decoders\WKT');

        $di->setters[V3\Formatter\Post\CSV::class]['setFilesystem'] = $di->lazyGet('tool.filesystem');


        // Repositories
        $di->set('repository.apikey', $di->lazyNew(Core\Ohanzee\Repositories\ApiKeyRepository::class));
        $di->set('repository.config', $di->lazyNew(Core\Ohanzee\Repositories\ConfigRepository::class));
        $di->set('repository.contact', $di->lazyNew(Core\Ohanzee\Repositories\ContactRepository::class));
        $di->set('repository.country_code', $di->lazyNew(Core\Ohanzee\Repositories\CountryCodeRepository::class));
        $di->set('repository.dataprovider', $di->lazyNew(Core\Ohanzee\Repositories\DataProviderRepository::class));
        $di->set('repository.form', $di->lazyNew(Core\Ohanzee\Repositories\FormRepository::class));
        $di->set('repository.form_role', $di->lazyNew(Core\Ohanzee\Repositories\Form\RoleRepository::class));
        $di->set('repository.form_contact', $di->lazyNew(Core\Ohanzee\Repositories\Form\ContactRepository::class));
        $di->set('repository.form_stats', $di->lazyNew(Core\Ohanzee\Repositories\Form\StatsRepository::class));

        $di->set('repository.form_stage', $di->lazyNew(Core\Ohanzee\Repositories\Form\StageRepository::class));
        $di->set('repository.form_attribute', $di->lazyNew(Core\Ohanzee\Repositories\Form\AttributeRepository::class));
        $di->set('repository.layer', $di->lazyNew(Core\Ohanzee\Repositories\LayerRepository::class));
        $di->set('repository.media', $di->lazyNew(Core\Ohanzee\Repositories\MediaRepository::class));
        $di->set('repository.message', $di->lazyNew(Core\Ohanzee\Repositories\MessageRepository::class));
        $di->set(
            'repository.targeted_survey_state',
            $di->lazyNew(Core\Ohanzee\Repositories\TargetedSurveyStateRepository::class)
        );
        $di->set('repository.post', $di->lazyNew(Core\Ohanzee\Repositories\PostRepository::class));

        $di->set('repository.post_lock', $di->lazyNew(Core\Ohanzee\Repositories\Post\LockRepository::class));
        $di->set('repository.tag', $di->lazyNew(Core\Ohanzee\Repositories\TagRepository::class));
        $di->set('repository.set', $di->lazyNew(Core\Ohanzee\Repositories\SetRepository::class));
        $di->set('repository.savedsearch', $di->lazyNew(
            Core\Ohanzee\Repositories\SetRepository::class,
            [],
            [
                'setSavedSearch' => true
            ]
        ));
        $di->set('repository.user', $di->lazyNew(Core\Ohanzee\Repositories\UserRepository::class));
        $di->set('repository.user_setting', $di->lazyNew(Core\Ohanzee\Repositories\User\SettingRepository::class));
        $di->set('repository.resetpassword', $di->lazyNew(Core\Ohanzee\Repositories\ResetPasswordRepository::class));
        $di->set('repository.role', $di->lazyNew(Core\Ohanzee\Repositories\RoleRepository::class));
        $di->set('repository.notification', $di->lazyNew(Core\Ohanzee\Repositories\NotificationRepository::class));
        $di->set('repository.webhook', $di->lazyNew(Core\Ohanzee\Repositories\WebhookRepository::class));
        $di->set('repository.csv', $di->lazyNew(Core\Ohanzee\Repositories\CSVRepository::class));
        $di->set(
            'repository.notification.queue',
            $di->lazyNew(Core\Ohanzee\Repositories\Notification\QueueRepository::class)
        );
        $di->set('repository.webhook.job', $di->lazyNew(Core\Ohanzee\Repositories\Webhook\JobRepository::class));
        $di->set('repository.permission', $di->lazyNew(Core\Ohanzee\Repositories\PermissionRepository::class));
        $di->set('repository.posts_export', $di->lazyNew(Core\Ohanzee\Repositories\Post\ExportRepository::class));
        $di->set('repository.tos', $di->lazyNew(Core\Ohanzee\Repositories\TosRepository::class));
        $di->set('repository.export_job', $di->lazyNew(Core\Ohanzee\Repositories\ExportJobRepository::class));
        $di->params[Core\Ohanzee\Repositories\ExportJobRepository::class] = [
            'post_repo' => $di->lazyGet('repository.post')
        ];
        $di->set('repository.export_batch', $di->lazyNew(Core\Ohanzee\Repositories\ExportBatchRepository::class));
        $di->setters[Core\Ohanzee\Repositories\Post\ExportRepository::class]['setSetRepo']
            = $di->lazyGet('repository.set');
        $di->setters[Core\Ohanzee\Repositories\Post\ExportRepository::class]['setTagRepo']
            = $di->lazyGet('repository.tag');
        $di->setters[Core\Ohanzee\Repositories\Post\ExportRepository::class]['setMessageRepo'] =
            $di->lazyGet('repository.message');
        $di->setters[Core\Ohanzee\Repositories\UserRepository::class]['setHasher']
            = $di->lazyGet('tool.hasher.password');

        // Repository parameters

        // Abstract repository parameters
        $di->params[Core\Ohanzee\Repositories\EloquentRepository::class] = [
            'resolver' => $di->lazyGet('db.eloquent.resolver'),
        ];
        $di->params[Core\Ohanzee\Repositories\OhanzeeRepository::class] = [
            'resolver' => $di->lazyGet('db.ohanzee.resolver'),
        ];

        // Config
        $di->params[Core\Ohanzee\Repositories\ConfigRepository::class] = [
            'resolver' => $di->lazyGet('db.ohanzee.resolver'),
        ];

        // Config
        $di->params[\App\PlatformVerifier\Database::class] = [
            'resolver' => $di->lazyGet('db.ohanzee.resolver'),
        ];
        // Set up Json Transcode Repository Trait
        $di->setters[Core\Ohanzee\Repositories\Concerns\JsonTranscode::class]['setTranscoder'] =
            $di->lazyGet('tool.jsontranscode');

        // Media repository parameters
        $di->params[Core\Ohanzee\Repositories\MediaRepository::class] = [
            'upload' => $di->lazyGet('tool.uploader'),
        ];

        // Form Stage repository parameters
        $di->params[Core\Ohanzee\Repositories\Form\StageRepository::class] = [
            'form_repo' => $di->lazyGet('repository.form')
        ];

        // Form Contact repository parameters
        $di->params[Core\Ohanzee\Repositories\Form\ContactRepository::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
            'targeted_survey_state_repo' => $di->lazyGet('repository.targeted_survey_state'),
            'message_repo' => $di->lazyGet('repository.message'),
        ];
        $di->setters[Core\Ohanzee\Repositories\Form\ContactRepository::class]['setEvent'] = 'FormContactEvent';

        // Form Stats repository parameters
        $di->params[Core\Ohanzee\Repositories\Form\StatsRepository::class] = [
            'form_repo' => $di->lazyGet('repository.form')
        ];

        // Webhook repo for Post listener
        $di->setters[V3\Listener\ContactListener::class]['setRepo'] =
            $di->lazyGet('repository.contact');
        // Webhook repo for Post listener
        $di->setters[V3\Listener\ContactListener::class]['setFormRepo'] =
            $di->lazyGet('repository.form');
        // Webhook repo for Post listener
        $di->setters[V3\Listener\ContactListener::class]['setFormAttributeRepo'] =
            $di->lazyGet('repository.form_attribute');

        // Webhook repo for Post listener
        $di->setters[V3\Listener\ContactListener::class]['setPostRepo'] =
            $di->lazyGet('repository.post');

        // Webhook repo for Post listener
        $di->setters[V3\Listener\ContactListener::class]['setMessageRepo'] =
            $di->lazyGet('repository.message');

        $di->setters[V3\Listener\ContactListener::class]['setTargetedSurveyStateRepo'] =
            $di->lazyGet('repository.targeted_survey_state');

        $di->setters[Core\Ohanzee\Repositories\Form\ContactRepository::class]['setListener'] =
            $di->lazyNew(V3\Listener\ContactListener::class);

        $di->setters[V3\Validator\Form\Contact\Create::class]['setFormRepo'] =
            $di->lazyGet('repository.form');

        // $di->setters[V3\Validator\Form\Contact\Create::class]['setContactRepo'] =
        //     $di->lazyGet('repository.contact');
        // $di->setters[V3\Validator\Form\Contact\Create::class]['setFormContactRepo'] =
        //     $di->lazyGet('repository.form_contact');


        // Form Attribute repository parameters
        $di->params[Core\Ohanzee\Repositories\Form\AttributeRepository::class] = [
            'form_stage_repo' => $di->lazyGet('repository.form_stage'),
            'form_repo' => $di->lazyGet('repository.form')
        ];

        // Post repository parameters
        $di->params[Core\Ohanzee\Repositories\PostRepository::class] = [
            'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
            'form_stage_repo' => $di->lazyGet('repository.form_stage'),
            'form_repo' => $di->lazyGet('repository.form'),
            'post_lock_repo' => $di->lazyGet('repository.post_lock'),
            'contact_repo' => $di->lazyGet('repository.contact'),
            'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
            'bounding_box_factory' => $di->newFactory(\Ushahidi\Core\Tool\BoundingBox::class)
        ];

        $di->set('repository.post.datetime', $di->lazyNew(Core\Ohanzee\Repositories\Post\DatetimeRepository::class));
        $di->set('repository.post.decimal', $di->lazyNew(Core\Ohanzee\Repositories\Post\DecimalRepository::class));
        $di->set('repository.post.geometry', $di->lazyNew(Core\Ohanzee\Repositories\Post\GeometryRepository::class));
        $di->set('repository.post.int', $di->lazyNew(Core\Ohanzee\Repositories\Post\IntegerRepository::class));
        $di->set('repository.post.point', $di->lazyNew(Core\Ohanzee\Repositories\Post\PointRepository::class));
        $di->set('repository.post.relation', $di->lazyNew(Core\Ohanzee\Repositories\Post\RelationRepository::class));
        $di->set('repository.post.text', $di->lazyNew(Core\Ohanzee\Repositories\Post\TextRepository::class));
        $di->set(
            'repository.post.description',
            $di->lazyNew(Core\Ohanzee\Repositories\Post\DescriptionRepository::class)
        );
        $di->set('repository.post.varchar', $di->lazyNew(Core\Ohanzee\Repositories\Post\VarcharRepository::class));
        $di->set('repository.post.markdown', $di->lazyNew(Core\Ohanzee\Repositories\Post\MarkdownRepository::class));
        $di->set('repository.post.title', $di->lazyNew(Core\Ohanzee\Repositories\Post\TitleRepository::class));
        $di->set('repository.post.media', $di->lazyNew(Core\Ohanzee\Repositories\Post\MediaRepository::class));
        $di->set('repository.post.tags', $di->lazyNew(Core\Ohanzee\Repositories\Post\TagsRepository::class));

        $di->params[Core\Ohanzee\Repositories\Post\TagsRepository::class] = [
            'tag_repo' => $di->lazyGet('repository.tag')
        ];

        // The post value repo factory
        $di->set('repository.post_value_factory', $di->lazyNew(Core\Ohanzee\Repositories\Post\ValueFactory::class));
        $di->params[Core\Ohanzee\Repositories\Post\ValueFactory::class] = [
            // a map of attribute types to repositories
            'map' => [
                'datetime' => $di->lazyGet('repository.post.datetime'),
                'decimal' => $di->lazyGet('repository.post.decimal'),
                'geometry' => $di->lazyGet('repository.post.geometry'),
                'int' => $di->lazyGet('repository.post.int'),
                'point' => $di->lazyGet('repository.post.point'),
                'relation' => $di->lazyGet('repository.post.relation'),
                'text' => $di->lazyGet('repository.post.text'),
                'description' => $di->lazyGet('repository.post.description'),
                'varchar' => $di->lazyGet('repository.post.varchar'),
                'markdown' => $di->lazyGet('repository.post.markdown'),
                'title' => $di->lazyGet('repository.post.title'),
                'media' => $di->lazyGet('repository.post.media'),
                'tags' => $di->lazyGet('repository.post.tags'),
            ],
        ];

        $di->params[Core\Ohanzee\Repositories\Post\PointRepository::class] = [
            'decoder' => $di->lazyNew('Symm\Gisconverter\Decoders\WKT')
        ];

        // Validators
        $di->set('validator.user.login', $di->lazyNew(V3\Validator\User\Login::class));
        $di->set('validator.contact.create', $di->lazyNew(V3\Validator\Contact\Create::class));
        $di->set('validator.contact.receive', $di->lazyNew(V3\Validator\Contact\Receive::class));

        $di->params[V3\Validator\Contact\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
        ];
        $di->params[V3\Validator\Contact\Receive::class] = [
            'repo' => $di->lazyGet('repository.user'),
        ];

        $di->params[V3\Validator\Config\Update::class] = [
            'available_providers' => $di->lazyGet('features.data-providers'),
        ];

        $di->params[V3\Validator\Tos\Create::class] = [
            'user_repo' => $di->lazyGet('repository.user')
        ];

        // Dependencies of validators
        $di->params[V3\Validator\Post\Create::class] = [
            'repo' => $di->lazyGet('repository.post'),
            'attribute_repo' => $di->lazyGet('repository.form_attribute'),
            'stage_repo' => $di->lazyGet('repository.form_stage'),
            'tag_repo' => $di->lazyGet('repository.tag'),
            'user_repo' => $di->lazyGet('repository.user'),
            'form_repo' => $di->lazyGet('repository.form'),
            'post_lock_repo' => $di->lazyGet('repository.post_lock'),
            'role_repo' => $di->lazyGet('repository.role'),
            'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
            'post_value_validator_factory' => $di->lazyGet('validator.post.value_factory'),
        ];

        $di->params[V3\Validator\Post\Lock\Update::class] = [
            'post_repo' => $di->lazyGet('repository.post_lock'),
        ];


        $di->params[V3\Validator\Form\Update::class] = [
            'repo' => $di->lazyGet('repository.form'),
        ];

        $di->params[V3\Validator\Form\Attribute\Update::class] = [
            'repo' => $di->lazyGet('repository.form_attribute'),
            'form_stage_repo' => $di->lazyGet('repository.form_stage'),
        ];
        $di->params[V3\Validator\Layer\Update::class] = [
            'media_repo' => $di->lazyGet('repository.media'),
        ];
        $di->params[V3\Validator\Message\Update::class] = [
            'repo' => $di->lazyGet('repository.message'),
        ];
        $di->params[V3\Validator\Message\Create::class] = [
            'repo' => $di->lazyGet('repository.message'),
            'user_repo' => $di->lazyGet('repository.user')
        ];

        $di->params[V3\Validator\Message\Receive::class] = [
            'repo' => $di->lazyGet('repository.message'),
        ];
        $di->set('validator.message.create', $di->lazyNew(V3\Validator\Message\Create::class));

        $di->params[V3\Validator\Set\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];
        $di->params[V3\Validator\Notification\Update::class] = [
            'user_repo' => $di->lazyGet('repository.user'),
            'collection_repo' => $di->lazyGet('repository.set'),
            'savedsearch_repo' => $di->lazyGet('repository.savedsearch'),
        ];
        $di->params[V3\Validator\Webhook\Update::class] = [
            'user_repo' => $di->lazyGet('repository.user'),
        ];
        $di->params[V3\Validator\SavedSearch\Create::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];
        $di->params[V3\Validator\SavedSearch\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];

        $di->params[V3\Validator\Set\Post\Create::class] = [
            'post_repo' => $di->lazyGet('repository.post')
        ];

        $di->params[V3\Validator\Tag\Update::class] = [
            'repo' => $di->lazyGet('repository.tag'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];

        $di->params[V3\Validator\User\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];
        $di->params[V3\Validator\User\Register::class] = [
            'repo' => $di->lazyGet('repository.user')
        ];
        $di->params[V3\Validator\User\Reset::class] = [
            'repo' => $di->lazyGet('repository.user')
        ];
        $di->params[V3\Validator\User\Setting\Update::class] = [
            'user_repo'    => $di->lazyGet('repository.user'),
            'user_setting_repo'    => $di->lazyGet('repository.user_setting')
        ];
        $di->params[V3\Validator\Contact\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
        ];
        $di->params[V3\Validator\CSV\Create::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->params[V3\Validator\CSV\Update::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->params[V3\Validator\Role\Update::class] = [
            'permission_repo' => $di->lazyGet('repository.permission')
        ];

        // Validator Setters
        $di->setters[V3\Validator\Form\Stage\Update::class] = [
            'setFormRepo' => $di->lazyGet('repository.form'),
        ];
        $di->setters[V3\Validator\Form\Role\Update::class] = [
            'setFormRepo' => $di->lazyGet('repository.form'),
            'setRoleRepo' => $di->lazyGet('repository.role'),
        ];
        $di->setters[V3\Validator\Media\Create::class] = [
            'setMaxBytes' => $di->lazy(function () {
                return config('media.max_upload_bytes');
            }),
        ];
        $di->setters[V3\Validator\CSV\Create::class] = [
            // @todo load from config
            'setMaxBytes' => '2048000',
        ];


        $di->set('validator.post.datetime', $di->lazyNew(V3\Validator\Post\Datetime::class));
        $di->set('validator.post.decimal', $di->lazyNew(V3\Validator\Post\Decimal::class));
        $di->set('validator.post.geometry', $di->lazyNew(V3\Validator\Post\Geometry::class));
        $di->set('validator.post.int', $di->lazyNew(V3\Validator\Post\Integer::class));
        $di->set('validator.post.link', $di->lazyNew(V3\Validator\Post\Link::class));
        $di->set('validator.post.point', $di->lazyNew(V3\Validator\Post\Point::class));
        $di->set('validator.post.relation', $di->lazyNew(V3\Validator\Post\Relation::class));
        $di->set('validator.post.varchar', $di->lazyNew(V3\Validator\Post\Varchar::class));
        $di->set('validator.post.markdown', $di->lazyNew(V3\Validator\Post\Markdown::class));
        $di->set('validator.post.video', $di->lazyNew(V3\Validator\Post\Video::class));
        $di->set('validator.post.title', $di->lazyNew(V3\Validator\Post\Title::class));
        $di->set('validator.post.media', $di->lazyNew(V3\Validator\Post\Media::class));
        $di->params[V3\Validator\Post\Media::class] = [
            'media_repo' => $di->lazyGet('repository.media')
        ];
        $di->set('validator.post.tags', $di->lazyNew(V3\Validator\Post\Tags::class));
        $di->params[V3\Validator\Post\Tags::class] = [
            'tags_repo' => $di->lazyGet('repository.tag')
        ];


        $di->set('validator.post.value_factory', $di->lazyNew(V3\Validator\Post\ValueFactory::class));
        $di->params[V3\Validator\Post\ValueFactory::class] = [
            // a map of attribute types to validators
            'map' => [
                'datetime' => $di->lazyGet('validator.post.datetime'),
                'decimal' => $di->lazyGet('validator.post.decimal'),
                'geometry' => $di->lazyGet('validator.post.geometry'),
                'int' => $di->lazyGet('validator.post.int'),
                'link' => $di->lazyGet('validator.post.link'),
                'point' => $di->lazyGet('validator.post.point'),
                'relation' => $di->lazyGet('validator.post.relation'),
                'varchar' => $di->lazyGet('validator.post.varchar'),
                'markdown' => $di->lazyGet('validator.post.markdown'),
                'title' => $di->lazyGet('validator.post.title'),
                'media' => $di->lazyGet('validator.post.media'),
                'video' => $di->lazyGet('validator.post.video'),
                'tags' => $di->lazyGet('validator.post.tags'),
            ],
        ];

        $di->params[V3\Validator\Post\Relation::class] = [
            'repo' => $di->lazyGet('repository.post')
        ];

        $di->set('transformer.csv', $di->lazyNew(V3\Transformer\CSVPostTransformer::class));
        // Post repo for mapping transformer
        $di->setters[V3\Transformer\CSVPostTransformer::class]['setRepo'] =
            $di->lazyGet('repository.post');

        // Event listener for the Set repo
        $di->setters[Core\Ohanzee\Repositories\SetRepository::class]['setEvent'] = 'PostSetEvent';

        $di->setters[Core\Ohanzee\Repositories\SetRepository::class]['setListener'] =
            $di->lazyNew(V3\Listener\PostSetListener::class);

        // NotificationQueue repo for Set listener
        $di->setters[V3\Listener\PostSetListener::class]['setRepo'] =
            $di->lazyGet('repository.notification.queue');

        // Event listener for the Post repo
        $di->setters[Core\Ohanzee\Repositories\PostRepository::class]['setEvent'] = 'PostCreateEvent';
        $di->setters[Core\Ohanzee\Repositories\PostRepository::class]['setListener'] =
            $di->lazyNew(V3\Listener\PostListener::class);

        // WebhookJob repo for Post listener
        $di->setters[V3\Listener\PostListener::class]['setRepo'] =
            $di->lazyGet('repository.webhook.job');

        // Webhook repo for Post listener
        $di->setters[V3\Listener\PostListener::class]['setWebhookRepo'] =
            $di->lazyGet('repository.webhook');

        // Add Intercom Listener to Config
        $di->setters[Core\Ohanzee\Repositories\ConfigRepository::class]['setEvent'] = 'ConfigUpdateEvent';
        $di->setters[Core\Ohanzee\Repositories\ConfigRepository::class]['setListener'] =
            $di->lazyNew(V3\Listener\IntercomCompanyListener::class);

        // Add Intercom Listener to Form
        $di->setters[Core\Ohanzee\Repositories\FormRepository::class]['setEvent'] = 'FormUpdateEvent';
        $di->setters[Core\Ohanzee\Repositories\FormRepository::class]['setListener'] =
            $di->lazyNew(V3\Listener\IntercomCompanyListener::class);

        // Add Intercom Listener to User
        $di->setters[Core\Ohanzee\Repositories\UserRepository::class]['setEvent'] = 'UserGetAllEvent';
        $di->setters[Core\Ohanzee\Repositories\UserRepository::class]['setListener'] =
            $di->lazyNew(V3\Listener\IntercomAdminListener::class);

        // Add Lock Listener
        $di->setters[Core\Ohanzee\Repositories\Post\LockRepository::class]['setEvent'] = 'LockBroken';

        $di->setters[Core\Usecase\Post\ImportPost::class]['setEvent'] = 'ImportPosts';
        $di->setters[Core\Usecase\Post\ImportPost::class]['setListener'] =
            $di->lazyNew(V3\Listener\Import::class);
        /**
         * HXL block
         */

        // generic authorizer for hxl
        $di->set('authorizer.hxl', $di->lazyNew('Ushahidi\Core\Tool\Authorizer\HXLAuthorizer'));

        $di->params[Ushahidi\Modules\V3\Factory\AuthorizerFactory::class]['map']['hxl'] =
            $di->lazyGet('authorizer.hxl');

        // hxl meta_data
        $di->set('repository.hxl_meta_data', $di->lazyNew(Core\Ohanzee\Repositories\HXL\HXLMetadataRepository::class));
        $di->set('formatter.entity.hxl_meta_data', $di->lazyNew(V3\Formatter\HXL\HXLMetadata::class));
        $di->set(
            'authorizer.hxl.meta_data',
            $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\HXLMetadataAuthorizer::class)
        );

        $di->setters[V3\Formatter\HXL\HXLMetadata::class]['setAuth']
            = $di->lazyGet("authorizer.hxl.meta_data");
        $di->params[\Ushahidi\Modules\V3\Factory\UsecaseFactory::class]['map']['hxl_meta_data'] = [
            'create' => $di->newFactory(Core\Usecase\HXL\Metadata\Create::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\AuthorizerFactory::class]['map']['hxl_meta_data'] =
            $di->lazyGet('authorizer.hxl.meta_data');
        $di->params[\Ushahidi\Modules\V3\Factory\RepositoryFactory::class]['map']['hxl_meta_data'] =
            $di->lazyGet('repository.hxl_meta_data');
        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['map']['hxl_meta_data'] =
            $di->lazyNew(V3\Formatter\HXL\HXLMetadata::class);
        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['hxl_meta_data'] = [
            'create' => $di->lazyNew(V3\Validator\HXL\Metadata\Create::class),
        ];

        $di->params[V3\Validator\ExportJob\Update::class] = [
            'repo' => $di->lazyGet('repository.export_job'),
            'hxl_meta_data_repo' => $di->lazyGet('repository.hxl_meta_data'),
            'user_repo' => $di->lazyGet('repository.user'),
        ];

        $di->params[V3\Validator\HXL\Metadata\Create::class] = [
            'repo' => $di->lazyGet('repository.hxl_meta_data'),
            'license_repo' => $di->lazyGet('repository.hxl_license'),
            'user_repo' => $di->lazyGet('repository.user'),
        ];

        $di->set(
            'formatter.entity.form_attribute_hxl_attribute_tag',
            $di->lazyNew(V3\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class)
        );
        $di->set(
            'authorizer.hxl.form_attribute_hxl_attribute_tag',
            $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\HXLAuthorizer::class)
        );
        $di->setters[V3\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class]['setAuth']
            = $di->lazyGet("authorizer.hxl");
        $di->params[\Ushahidi\Modules\V3\Factory\AuthorizerFactory::class]['map']['form_attribute_hxl_attribute_tag'] =
            $di->lazyGet('authorizer.hxl.form_attribute_hxl_attribute_tag');
        $di->params[\Ushahidi\Modules\V3\Factory\RepositoryFactory::class]['map']['form_attribute_hxl_attribute_tag'] =
            $di->lazyGet('repository.form_attribute_hxl_attribute_tag');
        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['map']['form_attribute_hxl_attribute_tag'] =
            $di->lazyNew(V3\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class);
        $di->setters[V3\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        $di->params[\Ushahidi\Modules\V3\Factory\ValidatorFactory::class]['map']['form_attribute_hxl_attribute_tag'] = [
            'create' => $di->lazyNew(V3\Validator\HXL\HXLFormAttributeHXLAttributeTag\Create::class),
        ];

        $di->params[V3\Validator\HXL\HXLFormAttributeHXLAttributeTag\Create::class] = [
            'repo' => $di->lazyGet('repository.form_attribute_hxl_attribute_tag'),
            'export_job_repo' => $di->lazyGet('repository.export_job'),
            'hxl_attribute_repo' => $di->lazyGet('repository.hxl_attribute'), //todo
            'hxl_tag_repo' => $di->lazyGet('repository.hxl_tag'),
            'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
        ];
        $di->setters[Core\Usecase\Export\Job\CreateJob::class]['setCreateHXLHeadingRowUsecase']
            = $di->lazy(function () {
                return service('factory.usecase')->get('form_attribute_hxl_attribute_tag', 'create');
            });

        $di->set(
            'repository.form_attribute_hxl_attribute_tag',
            $di->lazyNew(Core\Ohanzee\Repositories\HXL\HXLFormAttributeHXLAttributeTagRepository::class)
        );
        $di->setters[Core\Usecase\Post\ExportPost::class]['setFormAttributeRepository'] =
            $di->lazyGet('repository.form_attribute');

        // hxl attributes
        $di->set('repository.hxl_attribute', $di->lazyNew(Core\Ohanzee\Repositories\HXL\HXLAttributeRepository::class));
        $di->params[\Ushahidi\Modules\V3\Factory\RepositoryFactory::class]['map']['hxl_attributess'] =
            $di->lazyGet('repository.hxl_attribute');


        // hxl licenses
        $di->set('repository.hxl_license', $di->lazyNew(Core\Ohanzee\Repositories\HXL\HXLLicenseRepository::class));

        $di->set('formatter.entity.hxl_license', $di->lazyNew(V3\Formatter\HXL\HXLLicense::class));

        $di->params[\Ushahidi\Modules\V3\Factory\AuthorizerFactory::class]['map']['hxl_licenses'] =
            $di->lazyGet('authorizer.hxl');
        $di->params[\Ushahidi\Modules\V3\Factory\RepositoryFactory::class]['map']['hxl_licenses'] =
            $di->lazyGet('repository.hxl_license');
        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['map']['hxl_licenses'] =
            $di->lazyNew(V3\Formatter\HXL\HXLLicense::class);
        $di->setters[V3\Formatter\HXL\HXLLicense::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        // hxl tags
        $di->set('repository.hxl_tag', $di->lazyNew(Core\Ohanzee\Repositories\HXL\HXLTagRepository::class));
        $di->set('formatter.entity.hxl_tag', $di->lazyNew(V3\Formatter\HXL\HXLTag::class));
        $di->params[\Ushahidi\Modules\V3\Factory\AuthorizerFactory::class]['map']['hxl_tags'] =
            $di->lazyGet('authorizer.hxl');
        $di->params[\Ushahidi\Modules\V3\Factory\RepositoryFactory::class]['map']['hxl_tags'] =
            $di->lazyGet('repository.hxl_tag');
        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['map']['hxl_tags'] =
            $di->lazyNew(V3\Formatter\HXL\HXLTag::class);
        $di->setters[V3\Formatter\HXL\HXLTag::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        // repositories for Ushahidi\Core\Usecase\HXL\SendHXLUsecase
        $di->setters[Core\Usecase\HXL\SendHXLUsecase::class]['setExportJobRepository'] =
            $di->lazyGet('repository.export_job');
        $di->setters[Core\Usecase\HXL\SendHXLUsecase::class]['setUserHXLSettingsRepository'] =
            $di->lazyGet('repository.user_setting');
        $di->setters[Core\Usecase\HXL\SendHXLUsecase::class]['setHXLMetadataRepository'] =
            $di->lazyGet('repository.hxl_meta_data');
        $di->setters[Core\Usecase\HXL\SendHXLUsecase::class]['setHXLLicenseRepository'] =
            $di->lazyGet('repository.hxl_license');
        $di->setters[Core\Usecase\HXL\SendHXLUsecase::class]['setHXLFormAttributeHXLAttributeTagRepository'] =
            $di->lazyGet('repository.form_attribute_hxl_attribute_tag');

        // Add usecase for hxl_send

        $di->params[\Ushahidi\Modules\V3\Factory\RepositoryFactory::class]['map']['hxl_send'] =
            $di->lazyGet('repository.hxl_tag'); //FIXME
        $di->params[\Ushahidi\Modules\V3\Factory\UsecaseFactory::class]['map']['hxl_send'] = [
            'send' => $di->lazyNew(Core\Usecase\HXL\SendHXLUsecase::class),
        ];

        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['map']['hxl_send'] =
            $di->lazyNew(V3\Formatter\HXL\HXLSend::class);
        $di->params[\Ushahidi\Modules\V3\Factory\AuthorizerFactory::class]['map']['hxl_send'] =
            $di->lazyGet('authorizer.hxl');
        // add organisations
        $di->params[\Ushahidi\Modules\V3\Factory\UsecaseFactory::class]['map']['hxl_organisations'] = [
            'search' => $di->newFactory(Core\Usecase\HXL\Organisations\GetByUser::class),
        ];
        $di->params[\Ushahidi\Modules\V3\Factory\FormatterFactory::class]['map']['hxl_organisations'] =
            $di->lazyNew(V3\Formatter\HXL\HXLOrganisations::class);
        $di->params[\Ushahidi\Modules\V3\Factory\AuthorizerFactory::class]['map']['hxl_organisations'] =
            $di->lazyGet('authorizer.hxl');

        $di->setters[V3\Formatter\HXL\HXLOrganisations::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        $di->setters[Core\Usecase\HXL\Organisations\GetByUser::class]['setUserHXLSettingsRepository'] =
            $di->lazyGet('repository.user_setting');
        $di->setters[Core\Usecase\HXL\Organisations\GetByUser::class]['setRepository'] =
            null;
        $di->params[\Ushahidi\Modules\V3\Factory\RepositoryFactory::class]['map']['hxl_organisations'] =
            $di->lazyGet('repository.hxl_tag'); //FIXME

        // Authorizer
        $di->set('authorizer.config', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\ConfigAuthorizer::class));
        $di->set('authorizer.console', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\ConsoleAuthorizer::class));
        $di->set(
            'authorizer.dataprovider',
            $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\DataProviderAuthorizer::class)
        );
        $di->set('authorizer.form', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\FormAuthorizer::class, [
            'form_repo' => $di->lazyGet('repository.form'),
        ]));
        $di->params[\Ushahidi\Modules\V5\Policies\SurveyPolicy::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->set(
            'authorizer.form_attribute',
            $di->lazyNew(
                \Ushahidi\Core\Tool\Authorizer\FormAttributeAuthorizer::class,
                [
                'stage_repo' => $di->lazyGet('repository.form_stage'),
                'stage_auth' => $di->lazyGet('authorizer.form_stage'),
                ]
            )
        );
        $di->set('authorizer.form_role', $di->lazyNew(
            \Ushahidi\Core\Tool\Authorizer\FormRoleAuthorizer::class,
            [
                'form_repo' => $di->lazyGet('repository.form'),
                'form_auth' => $di->lazyGet('authorizer.form'),
            ]
        ));
        $di->set(
            'authorizer.form_stage',
            $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\FormStageAuthorizer::class, [
                'form_repo' => $di->lazyGet('repository.form'),
                'form_auth' => $di->lazyGet('authorizer.form'),
            ])
        );
        $di->set('authorizer.form_contact', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\FormContactAuthorizer::class, [
            'form_repo' => $di->lazyGet('repository.form'),
            'form_auth' => $di->lazyGet('authorizer.form'),
        ]));
        $di->set('authorizer.form_stats', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\FormStatsAuthorizer::class));
        $di->set('authorizer.user', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\UserAuthorizer::class));
        $di->set('authorizer.user_setting', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\UserSettingAuthorizer::class));
        $di->set('authorizer.layer', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\LayerAuthorizer::class));
        $di->set('authorizer.media', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\MediaAuthorizer::class));
        $di->set('authorizer.message', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\MessageAuthorizer::class));
        $di->set('authorizer.tag', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\TagAuthorizer::class));
        $di->set('authorizer.savedsearch', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\SetAuthorizer::class));
        $di->set('authorizer.set', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\SetAuthorizer::class));
        $di->set(
            'authorizer.notification',
            $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\NotificationAuthorizer::class)
        );
        $di->set('authorizer.webhook', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\WebhookAuthorizer::class));
        $di->set('authorizer.apikey', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\ApiKeyAuthorizer::class));
        $di->set('authorizer.contact', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\ContactAuthorizer::class));
        $di->set('authorizer.csv', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\CSVAuthorizer::class));
        $di->set('authorizer.role', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\RoleAuthorizer::class));
        $di->set('authorizer.permission', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\PermissionAuthorizer::class));
        $di->set('authorizer.post', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\PostAuthorizer::class));
        $di->set('authorizer.post_lock', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\PostAuthorizer::class));
        $di->set('authorizer.tos', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\TosAuthorizer::class));
        $di->set('authorizer.external_auth', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\ExternalAuthorizer::class));
        $di->set('authorizer.export_job', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\ExportJobAuthorizer::class));
        $di->params[\Ushahidi\Core\Tool\Authorizer\PostAuthorizer::class] = [
            'post_repo' => $di->lazyGet('repository.post'),
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->params[\Ushahidi\Core\Tool\Authorizer\TagAuthorizer::class] = [
            'tag_repo' => $di->lazyGet('repository.tag'),
        ];

        $di->set('authorizer.country_code', $di->lazyNew(\Ushahidi\Core\Tool\Authorizer\CountryCodeAuthorizer::class));

        //FIXME
        $di->set('repository.hxl_organisations', $di->lazyNew(Core\Ohanzee\Repositories\HXL\HXLTagRepository::class));

        // Set up config bindings

        // Map
        // Site config
        $di->values['map.config'] = $di->lazy(
            function ($repo) {
                return $repo->get('map')->asArray();
            },
            $di->lazyGet('repository.config')
        );

        $di->set('map.config', $di->lazyValue('map.config'));

        // Dataprovider feature config
        $di->values['features.data-providers'] = $di->lazy(
            function ($repo) {
                $config = $repo->get('features')->asArray();
                return array_filter($config['data-providers']);
            },
            $di->lazyGet('repository.config')
        );

        $di->set('features.data-providers', $di->lazyValue('features.data-providers'));
    }
}
