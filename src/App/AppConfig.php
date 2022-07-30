<?php

namespace Ushahidi\App;

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
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['apikeys'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\ApiKey\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\ApiKey\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['config'] = [
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Config\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['forms'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Form\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Form\Update::class),
            'delete' => $di->lazyNew(\Ushahidi\App\Validator\Form\Delete::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['form_attributes'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Form\Attribute\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Form\Attribute\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['form_roles'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Form\Role\Create::class),
            'update_collection' => $di->lazyNew(\Ushahidi\App\Validator\Form\Role\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['form_contacts'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Form\Contact\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Form\Contact\Update::class),
        ];

        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['form_stages'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Form\Stage\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Form\Stage\Update::class),
            'delete' => $di->lazyNew(\Ushahidi\App\Validator\Form\Stage\Delete::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['layers'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Layer\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Layer\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['media'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Media\Create::class),
            'delete' => $di->lazyNew(\Ushahidi\App\Validator\Media\Delete::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['posts'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Post\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Post\Update::class),
            'import' => $di->lazyNew(\Ushahidi\App\Validator\Post\Import::class),
            'export' => $di->lazyNew(\Ushahidi\App\Validator\Post\Export::class),
            'webhook-update' => $di->lazyNew(\Ushahidi\App\Validator\Post\Create::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['export_jobs'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\ExportJob\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\ExportJob\Update::class),
        ];

        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['posts_lock'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Post\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Post\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['tags'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Tag\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Tag\Update::class),
            'delete' => $di->lazyNew(\Ushahidi\App\Validator\Tag\Delete::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['users'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\User\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\User\Update::class),
            'register' => $di->lazyNew(\Ushahidi\App\Validator\User\Register::class),
            'passwordreset' => $di->lazyNew(\Ushahidi\App\Validator\User\Reset::class)
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['user_settings'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\User\Setting\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\User\Setting\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['messages'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Message\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Message\Update::class),
            'receive' => $di->lazyNew(\Ushahidi\App\Validator\Message\Receive::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['savedsearches'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\SavedSearch\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\SavedSearch\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['sets'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Set\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Set\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['notifications'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Notification\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Notification\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['webhooks'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Webhook\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Webhook\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['contacts'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Contact\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Contact\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['sets_posts'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Set\Post\Create::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['csv'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\CSV\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\CSV\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['csv'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\CSV\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\CSV\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['roles'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Role\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Role\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['permissions'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Permission\Create::class),
            'update' => $di->lazyNew(\Ushahidi\App\Validator\Permission\Update::class),
        ];
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['tos'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\Tos\Create::class),
        ];

        // Formatter mapping
        $di->params[\Ushahidi\Factory\FormatterFactory::class]['map'] = [
            'apikeys'              => $di->lazyNew(\Ushahidi\App\Formatter\ApiKey::class),
            'config'               => $di->lazyNew(\Ushahidi\App\Formatter\Config::class),
            'dataproviders'        => $di->lazyNew(\Ushahidi\App\Formatter\Dataprovider::class),
            'country_codes'        => $di->lazyNew(\Ushahidi\App\Formatter\CountryCode::class),
            'export_jobs'          => $di->lazyNew(\Ushahidi\App\Formatter\ExportJob::class),
            'forms'                => $di->lazyNew(\Ushahidi\App\Formatter\Form::class),
            'form_attributes'      => $di->lazyNew(\Ushahidi\App\Formatter\Form\Attribute::class),
            'form_roles'           => $di->lazyNew(\Ushahidi\App\Formatter\Form\Role::class),
            'form_stages'          => $di->lazyNew(\Ushahidi\App\Formatter\Form\Stage::class),
            'form_contacts'        => $di->lazyNew(\Ushahidi\App\Formatter\Form\Contact::class),
            'form_stats'           => $di->lazyNew(\Ushahidi\App\Formatter\Form\Stats::class),
            'layers'               => $di->lazyNew(\Ushahidi\App\Formatter\Layer::class),
            'media'                => $di->lazyNew(\Ushahidi\App\Formatter\Media::class),
            'messages'             => $di->lazyNew(\Ushahidi\App\Formatter\Message::class),
            'posts'                => $di->lazyNew(\Ushahidi\App\Formatter\Post::class),
            'posts_lock'           => $di->lazyNew(\Ushahidi\App\Formatter\Post\Lock::class),
            'tags'                 => $di->lazyNew(\Ushahidi\App\Formatter\Tag::class),
            'savedsearches'        => $di->lazyNew(\Ushahidi\App\Formatter\Set::class),
            'sets'                 => $di->lazyNew(\Ushahidi\App\Formatter\Set::class),
            'sets_posts'           => $di->lazyNew(\Ushahidi\App\Formatter\Post::class),
            'savedsearches_posts'  => $di->lazyNew(\Ushahidi\App\Formatter\Post::class),
            'users'                => $di->lazyNew(\Ushahidi\App\Formatter\User::class),
            'user_settings'        => $di->lazyNew(\Ushahidi\App\Formatter\User\Setting::class),
            'notifications'        => $di->lazyNew(\Ushahidi\App\Formatter\Notification::class),
            'webhooks'             => $di->lazyNew(\Ushahidi\App\Formatter\Webhook::class),
            'contacts'             => $di->lazyNew(\Ushahidi\App\Formatter\Contact::class),
            'csv'                  => $di->lazyNew(\Ushahidi\App\Formatter\CSV::class),
            'roles'                => $di->lazyNew(\Ushahidi\App\Formatter\Role::class),
            'permissions'          => $di->lazyNew(\Ushahidi\App\Formatter\Permission::class),
            // Formatter for post exports. Defaults to CSV export
            'posts_export'         => $di->lazyNew(\Ushahidi\App\Formatter\Post\CSV::class),
            'tos' => $di->lazyNew(\Ushahidi\App\Formatter\Tos::class),
        ];

        // Formatter parameters
        $di->setters[\Ushahidi\App\Formatter\ApiKey::class]['setAuth'] = $di->lazyGet("authorizer.apikey");
        $di->setters[\Ushahidi\App\Formatter\Config::class]['setAuth'] = $di->lazyGet("authorizer.config");
        $di->setters[\Ushahidi\App\Formatter\CSV::class]['setAuth'] = $di->lazyGet("authorizer.csv");
        $di->setters[\Ushahidi\App\Formatter\Dataprovider::class]['setAuth'] = $di->lazyGet("authorizer.dataprovider");
        $di->setters[\Ushahidi\App\Formatter\ExportJob::class]['setAuth'] = $di->lazyGet("authorizer.export_job");
        $di->setters[\Ushahidi\App\Formatter\Form::class]['setAuth'] = $di->lazyGet("authorizer.form");
        $di->setters[\Ushahidi\App\Formatter\Form\Attribute::class]['setAuth']
            = $di->lazyGet("authorizer.form_attribute");
        $di->setters[\Ushahidi\App\Formatter\Form\Role::class]['setAuth'] = $di->lazyGet("authorizer.form_role");
        $di->setters[\Ushahidi\App\Formatter\Form\Stage::class]['setAuth'] = $di->lazyGet("authorizer.form_stage");
        $di->setters[\Ushahidi\App\Formatter\Layer::class]['setAuth'] = $di->lazyGet("authorizer.layer");
        $di->setters[\Ushahidi\App\Formatter\Media::class]['setAuth'] = $di->lazyGet("authorizer.media");
        $di->setters[\Ushahidi\App\Formatter\Message::class]['setAuth'] = $di->lazyGet("authorizer.message");
        $di->setters[\Ushahidi\App\Formatter\Post::class]['setAuth'] = $di->lazyGet("authorizer.post");
        $di->setters[\Ushahidi\App\Formatter\Post\Lock::class]['setAuth'] = $di->lazyGet("authorizer.post");
        $di->setters[\Ushahidi\App\Formatter\Tag::class]['setAuth'] = $di->lazyGet("authorizer.tag");
        $di->setters[\Ushahidi\App\Formatter\Tos::class]['setAuth'] = $di->lazyGet("authorizer.tos");
        $di->setters[\Ushahidi\App\Formatter\User::class]['setAuth'] = $di->lazyGet("authorizer.user");
        $di->setters[\Ushahidi\App\Formatter\User\Setting::class]['setAuth'] = $di->lazyGet("authorizer.user_setting");
        $di->setters[\Ushahidi\App\Formatter\Savedsearch::class]['setAuth'] = $di->lazyGet("authorizer.savedsearch");
        $di->setters[\Ushahidi\App\Formatter\Set::class]['setAuth'] = $di->lazyGet("authorizer.set");
        $di->setters[\Ushahidi\App\Formatter\Set\Post::class]['setAuth'] = $di->lazyGet("authorizer.set_post");
        $di->setters[\Ushahidi\App\Formatter\Notification::class]['setAuth'] = $di->lazyGet("authorizer.notification");
        $di->setters[\Ushahidi\App\Formatter\Webhook::class]['setAuth'] = $di->lazyGet("authorizer.webhook");
        $di->setters[\Ushahidi\App\Formatter\Contact::class]['setAuth'] = $di->lazyGet("authorizer.contact");
        $di->setters[\Ushahidi\App\Formatter\Role::class]['setAuth'] = $di->lazyGet("authorizer.role");
        $di->setters[\Ushahidi\App\Formatter\Permission::class]['setAuth'] = $di->lazyGet("authorizer.permission");
        $di->setters[\Ushahidi\App\Formatter\Form\Stats::class]['setAuth'] = $di->lazyGet("authorizer.form_stats");
        $di->setters[\Ushahidi\App\Formatter\CountryCode::class]['setAuth'] = $di->lazyGet("authorizer.country_code");


        // Set Formatter factory
        $di->params[\Ushahidi\Factory\FormatterFactory::class]['factory']
            = $di->newFactory(\Ushahidi\App\Formatter\Collection::class);


        $di->set('tool.jsontranscode', $di->lazyNew(\Ushahidi\Core\Tools\JsonTranscode::class));

        // Formatters
        $di->set('formatter.entity.api', $di->lazyNew(\Ushahidi\App\Formatter\API::class));
        $di->set('formatter.entity.country_code', $di->lazyNew(\Ushahidi\App\Formatter\CountryCode::class));
        $di->set('formatter.entity.console', $di->lazyNew(\Ushahidi\App\Formatter\Console::class));
        $di->set('formatter.entity.form.contact', $di->lazyNew(\Ushahidi\App\Formatter\Form\Contact::class));
        $di->set('formatter.entity.form.stats', $di->lazyNew(\Ushahidi\App\Formatter\Form\Stats::class));
        $di->set(
            'formatter.entity.form.contactcollection',
            $di->lazyNew(\Ushahidi\App\Formatter\Form\ContactCollection::class)
        );
        $di->set('formatter.entity.post.value', $di->lazyNew(\Ushahidi\App\Formatter\PostValue::class));
        $di->set('formatter.entity.post.lock', $di->lazyNew(\Ushahidi\App\Formatter\Post\Lock::class));
        $di->set('formatter.entity.post.geojson', $di->lazyNew(\Ushahidi\App\Formatter\Post\GeoJSON::class));
        $di->set(
            'formatter.entity.post.geojsoncollection',
            $di->lazyNew(\Ushahidi\App\Formatter\Post\GeoJSONCollection::class)
        );
        $di->set('formatter.entity.post.stats', $di->lazyNew(\Ushahidi\App\Formatter\Post\Stats::class));
        $di->set('formatter.entity.post.csv', $di->lazyNew(\Ushahidi\App\Formatter\Post\CSV::class));

        $di->set('formatter.output.json', $di->lazyNew(\Ushahidi\App\Formatter\JSON::class));
        $di->set('formatter.output.jsonp', $di->lazyNew(\Ushahidi\App\Formatter\JSONP::class));

        // Formatter parameters
        $di->setters[\Ushahidi\App\Formatter\JSONP::class]['setCallback'] = function () {
            return Request::current()->query('callback');
        };
        $di->params[\Ushahidi\App\Formatter\Post::class] = [
            'value_formatter' => $di->lazyGet('formatter.entity.post.value')
        ];
        $di->setters[\Ushahidi\App\Formatter\Post\GeoJSON::class]['setDecoder']
            = $di->lazyNew('Symm\Gisconverter\Decoders\WKT');
        $di->setters[\Ushahidi\App\Formatter\Post\GeoJSONCollection::class]['setDecoder'] =
            $di->lazyNew('Symm\Gisconverter\Decoders\WKT');

        $di->setters[\Ushahidi\App\Formatter\Post\CSV::class]['setFilesystem'] = $di->lazyGet('tool.filesystem');


        // Repositories
        $di->set('repository.apikey', $di->lazyNew(\Ushahidi\App\Repository\ApiKeyRepository::class));
        $di->set('repository.config', $di->lazyNew(\Ushahidi\App\Repository\ConfigRepository::class));
        $di->set('repository.contact', $di->lazyNew(\Ushahidi\App\Repository\ContactRepository::class));
        $di->set('repository.country_code', $di->lazyNew(\Ushahidi\App\Repository\CountryCodeRepository::class));
        $di->set('repository.dataprovider', $di->lazyNew(\Ushahidi\App\Repository\DataproviderRepository::class));
        $di->set('repository.form', $di->lazyNew(\Ushahidi\App\Repository\FormRepository::class));
        $di->set('repository.form_role', $di->lazyNew(\Ushahidi\App\Repository\Form\RoleRepository::class));
        $di->set('repository.form_contact', $di->lazyNew(\Ushahidi\App\Repository\Form\ContactRepository::class));
        $di->set('repository.form_stats', $di->lazyNew(\Ushahidi\App\Repository\Form\StatsRepository::class));

        $di->set('repository.form_stage', $di->lazyNew(\Ushahidi\App\Repository\Form\StageRepository::class));
        $di->set('repository.form_attribute', $di->lazyNew(\Ushahidi\App\Repository\Form\AttributeRepository::class));
        $di->set('repository.layer', $di->lazyNew(\Ushahidi\App\Repository\LayerRepository::class));
        $di->set('repository.media', $di->lazyNew(\Ushahidi\App\Repository\MediaRepository::class));
        $di->set('repository.message', $di->lazyNew(\Ushahidi\App\Repository\MessageRepository::class));
        $di->set(
            'repository.targeted_survey_state',
            $di->lazyNew(\Ushahidi\App\Repository\TargetedSurveyStateRepository::class)
        );
        $di->set('repository.post', $di->lazyNew(\Ushahidi\App\Repository\PostRepository::class));

        $di->set('repository.post_lock', $di->lazyNew(\Ushahidi\App\Repository\Post\LockRepository::class));
        $di->set('repository.tag', $di->lazyNew(\Ushahidi\App\Repository\TagRepository::class));
        $di->set('repository.set', $di->lazyNew(\Ushahidi\App\Repository\SetRepository::class));
        $di->set('repository.savedsearch', $di->lazyNew(
            \Ushahidi\App\Repository\SetRepository::class,
            [],
            [
                'setSavedSearch' => true
            ]
        ));
        $di->set('repository.user', $di->lazyNew(\Ushahidi\App\Repository\UserRepository::class));
        $di->set('repository.user_setting', $di->lazyNew(\Ushahidi\App\Repository\User\SettingRepository::class));
        $di->set('repository.resetpassword', $di->lazyNew(\Ushahidi\App\Repository\ResetPasswordRepository::class));
        $di->set('repository.role', $di->lazyNew(\Ushahidi\App\Repository\RoleRepository::class));
        $di->set('repository.notification', $di->lazyNew(\Ushahidi\App\Repository\NotificationRepository::class));
        $di->set('repository.webhook', $di->lazyNew(\Ushahidi\App\Repository\WebhookRepository::class));
        $di->set('repository.csv', $di->lazyNew(\Ushahidi\App\Repository\CSVRepository::class));
        $di->set(
            'repository.notification.queue',
            $di->lazyNew(\Ushahidi\App\Repository\Notification\QueueRepository::class)
        );
        $di->set('repository.webhook.job', $di->lazyNew(\Ushahidi\App\Repository\Webhook\JobRepository::class));
        $di->set('repository.permission', $di->lazyNew(\Ushahidi\App\Repository\PermissionRepository::class));
        $di->set('repository.posts_export', $di->lazyNew(\Ushahidi\App\Repository\Post\ExportRepository::class));
        $di->set('repository.tos', $di->lazyNew(\Ushahidi\App\Repository\TosRepository::class));
        $di->set('repository.export_job', $di->lazyNew(\Ushahidi\App\Repository\ExportJobRepository::class));
        $di->params[\Ushahidi\App\Repository\ExportJobRepository::class] = [
            'post_repo' => $di->lazyGet('repository.post')
        ];
        $di->set('repository.export_batch', $di->lazyNew(\Ushahidi\App\Repository\ExportBatchRepository::class));
        $di->setters[\Ushahidi\App\Repository\Post\ExportRepository::class]['setSetRepo']
            = $di->lazyGet('repository.set');
        $di->setters[\Ushahidi\App\Repository\Post\ExportRepository::class]['setTagRepo']
            = $di->lazyGet('repository.tag');
        $di->setters[\Ushahidi\App\Repository\Post\ExportRepository::class]['setMessageRepo'] =
            $di->lazyGet('repository.message');
        $di->setters[\Ushahidi\App\Repository\UserRepository::class]['setHasher']
            = $di->lazyGet('tool.hasher.password');

        // Repository parameters

        // Abstract repository parameters
        $di->params[\Ushahidi\App\Repository\EloquentRepository::class] = [
            'resolver' => $di->lazyGet('db.eloquent.resolver'),
        ];
        $di->params[\Ushahidi\App\Repository\OhanzeeRepository::class] = [
            'resolver' => $di->lazyGet('db.ohanzee.resolver'),
        ];

        // Config
        $di->params[\Ushahidi\App\Repository\ConfigRepository::class] = [
            'resolver' => $di->lazyGet('db.ohanzee.resolver'),
        ];

        // Config
        $di->params[\Ushahidi\App\PlatformVerifier\Database::class] = [
            'resolver' => $di->lazyGet('db.ohanzee.resolver'),
        ];
        // Set up Json Transcode Repository Trait
        $di->setters[\Ushahidi\App\Repository\Concerns\JsonTranscode::class]['setTranscoder'] =
            $di->lazyGet('tool.jsontranscode');

        // Media repository parameters
        $di->params[\Ushahidi\App\Repository\MediaRepository::class] = [
            'upload' => $di->lazyGet('tool.uploader'),
        ];

        // Form Stage repository parameters
        $di->params[\Ushahidi\App\Repository\Form\StageRepository::class] = [
            'form_repo' => $di->lazyGet('repository.form')
        ];

        // Form Contact repository parameters
        $di->params[\Ushahidi\App\Repository\Form\ContactRepository::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
            'targeted_survey_state_repo' => $di->lazyGet('repository.targeted_survey_state'),
            'message_repo' => $di->lazyGet('repository.message'),
        ];
        $di->setters[\Ushahidi\App\Repository\Form\ContactRepository::class]['setEvent'] = 'FormContactEvent';

        // Form Stats repository parameters
        $di->params[\Ushahidi\App\Repository\Form\StatsRepository::class] = [
            'form_repo' => $di->lazyGet('repository.form')
        ];

        // Webhook repo for Post listener
        $di->setters[\Ushahidi\App\Listener\ContactListener::class]['setRepo'] =
            $di->lazyGet('repository.contact');
        // Webhook repo for Post listener
        $di->setters[\Ushahidi\App\Listener\ContactListener::class]['setFormRepo'] =
            $di->lazyGet('repository.form');
        // Webhook repo for Post listener
        $di->setters[\Ushahidi\App\Listener\ContactListener::class]['setFormAttributeRepo'] =
            $di->lazyGet('repository.form_attribute');

        // Webhook repo for Post listener
        $di->setters[\Ushahidi\App\Listener\ContactListener::class]['setPostRepo'] =
            $di->lazyGet('repository.post');

        // Webhook repo for Post listener
        $di->setters[\Ushahidi\App\Listener\ContactListener::class]['setMessageRepo'] =
            $di->lazyGet('repository.message');

        $di->setters[\Ushahidi\App\Listener\ContactListener::class]['setTargetedSurveyStateRepo'] =
            $di->lazyGet('repository.targeted_survey_state');

        $di->setters[\Ushahidi\App\Repository\Form\ContactRepository::class]['setListener'] =
            $di->lazyNew(\Ushahidi\App\Listener\ContactListener::class);

        $di->setters[\Ushahidi\App\Validator\Form\Contact\Create::class]['setFormRepo'] =
            $di->lazyGet('repository.form');

        $di->setters[\Ushahidi\App\Validator\Form\Contact\Create::class]['setContactRepo'] =
            $di->lazyGet('repository.contact');
        $di->setters[\Ushahidi\App\Validator\Form\Contact\Create::class]['setFormContactRepo'] =
            $di->lazyGet('repository.form_contact');


        // Form Attribute repository parameters
        $di->params[\Ushahidi\App\Repository\Form\AttributeRepository::class] = [
            'form_stage_repo' => $di->lazyGet('repository.form_stage'),
            'form_repo' => $di->lazyGet('repository.form')
        ];

        // Post repository parameters
        $di->params[\Ushahidi\App\Repository\PostRepository::class] = [
            'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
            'form_stage_repo' => $di->lazyGet('repository.form_stage'),
            'form_repo' => $di->lazyGet('repository.form'),
            'post_lock_repo' => $di->lazyGet('repository.post_lock'),
            'contact_repo' => $di->lazyGet('repository.contact'),
            'post_value_factory' => $di->lazyGet('repository.post_value_factory'),
            'bounding_box_factory' => $di->newFactory(\Ushahidi\Core\Tools\BoundingBox::class)
        ];

        $di->set('repository.post.datetime', $di->lazyNew(\Ushahidi\App\Repository\Post\DatetimeRepository::class));
        $di->set('repository.post.decimal', $di->lazyNew(\Ushahidi\App\Repository\Post\DecimalRepository::class));
        $di->set('repository.post.geometry', $di->lazyNew(\Ushahidi\App\Repository\Post\GeometryRepository::class));
        $di->set('repository.post.int', $di->lazyNew(\Ushahidi\App\Repository\Post\IntegerRepository::class));
        $di->set('repository.post.point', $di->lazyNew(\Ushahidi\App\Repository\Post\PointRepository::class));
        $di->set('repository.post.relation', $di->lazyNew(\Ushahidi\App\Repository\Post\RelationRepository::class));
        $di->set('repository.post.text', $di->lazyNew(\Ushahidi\App\Repository\Post\TextRepository::class));
        $di->set(
            'repository.post.description',
            $di->lazyNew(\Ushahidi\App\Repository\Post\DescriptionRepository::class)
        );
        $di->set('repository.post.varchar', $di->lazyNew(\Ushahidi\App\Repository\Post\VarcharRepository::class));
        $di->set('repository.post.markdown', $di->lazyNew(\Ushahidi\App\Repository\Post\MarkdownRepository::class));
        $di->set('repository.post.title', $di->lazyNew(\Ushahidi\App\Repository\Post\TitleRepository::class));
        $di->set('repository.post.media', $di->lazyNew(\Ushahidi\App\Repository\Post\MediaRepository::class));
        $di->set('repository.post.tags', $di->lazyNew(\Ushahidi\App\Repository\Post\TagsRepository::class));

        $di->params[\Ushahidi\App\Repository\Post\TagsRepository::class] = [
            'tag_repo' => $di->lazyGet('repository.tag')
        ];

        // The post value repo factory
        $di->set('repository.post_value_factory', $di->lazyNew(\Ushahidi\App\Repository\Post\ValueFactory::class));
        $di->params[\Ushahidi\App\Repository\Post\ValueFactory::class] = [
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

        $di->params[\Ushahidi\App\Repository\Post\PointRepository::class] = [
            'decoder' => $di->lazyNew('Symm\Gisconverter\Decoders\WKT')
        ];

        // Validators
        $di->set('validator.user.login', $di->lazyNew(\Ushahidi\App\Validator\User\Login::class));
        $di->set('validator.contact.create', $di->lazyNew(\Ushahidi\App\Validator\Contact\Create::class));
        $di->set('validator.contact.receive', $di->lazyNew(\Ushahidi\App\Validator\Contact\Receive::class));

        $di->params[\Ushahidi\App\Validator\Contact\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
        ];
        $di->params[\Ushahidi\App\Validator\Contact\Receive::class] = [
            'repo' => $di->lazyGet('repository.user'),
        ];

        $di->params[\Ushahidi\App\Validator\Config\Update::class] = [
            'available_providers' => $di->lazyGet('features.data-providers'),
        ];

        $di->params[\Ushahidi\App\Validator\Tos\Create::class] = [
            'user_repo' => $di->lazyGet('repository.user')
        ];

        // Dependencies of validators
        $di->params[\Ushahidi\App\Validator\Post\Create::class] = [
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

        $di->params[\Ushahidi\App\Validator\Post\Lock\Update::class] = [
            'post_repo' => $di->lazyGet('repository.post_lock'),
        ];


        $di->params[\Ushahidi\App\Validator\Form\Update::class] = [
            'repo' => $di->lazyGet('repository.form'),
        ];

        $di->params[\Ushahidi\App\Validator\Form\Attribute\Update::class] = [
            'repo' => $di->lazyGet('repository.form_attribute'),
            'form_stage_repo' => $di->lazyGet('repository.form_stage'),
        ];
        $di->params[\Ushahidi\App\Validator\Layer\Update::class] = [
            'media_repo' => $di->lazyGet('repository.media'),
        ];
        $di->params[\Ushahidi\App\Validator\Message\Update::class] = [
            'repo' => $di->lazyGet('repository.message'),
        ];
        $di->params[\Ushahidi\App\Validator\Message\Create::class] = [
            'repo' => $di->lazyGet('repository.message'),
            'user_repo' => $di->lazyGet('repository.user')
        ];

        $di->params[\Ushahidi\App\Validator\Message\Receive::class] = [
            'repo' => $di->lazyGet('repository.message'),
        ];
        $di->set('validator.message.create', $di->lazyNew(\Ushahidi\App\Validator\Message\Create::class));

        $di->params[\Ushahidi\App\Validator\Set\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];
        $di->params[\Ushahidi\App\Validator\Notification\Update::class] = [
            'user_repo' => $di->lazyGet('repository.user'),
            'collection_repo' => $di->lazyGet('repository.set'),
            'savedsearch_repo' => $di->lazyGet('repository.savedsearch'),
        ];
        $di->params[\Ushahidi\App\Validator\Webhook\Update::class] = [
            'user_repo' => $di->lazyGet('repository.user'),
        ];
        $di->params[\Ushahidi\App\Validator\SavedSearch\Create::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];
        $di->params[\Ushahidi\App\Validator\SavedSearch\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];

        $di->params[\Ushahidi\App\Validator\Set\Post\Create::class] = [
            'post_repo' => $di->lazyGet('repository.post')
        ];

        $di->params[\Ushahidi\App\Validator\Tag\Update::class] = [
            'repo' => $di->lazyGet('repository.tag'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];

        $di->params[\Ushahidi\App\Validator\User\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
            'role_repo' => $di->lazyGet('repository.role'),
        ];
        $di->params[\Ushahidi\App\Validator\User\Register::class] = [
            'repo' => $di->lazyGet('repository.user')
        ];
        $di->params[\Ushahidi\App\Validator\User\Reset::class] = [
            'repo' => $di->lazyGet('repository.user')
        ];
        $di->params[\Ushahidi\App\Validator\User\Setting\Update::class] = [
            'user_repo'    => $di->lazyGet('repository.user'),
            'user_setting_repo'    => $di->lazyGet('repository.user_setting')
        ];
        $di->params[\Ushahidi\App\Validator\Contact\Update::class] = [
            'repo' => $di->lazyGet('repository.user'),
        ];
        $di->params[\Ushahidi\App\Validator\CSV\Create::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->params[\Ushahidi\App\Validator\CSV\Update::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->params[\Ushahidi\App\Validator\Role\Update::class] = [
            'permission_repo' => $di->lazyGet('repository.permission')
        ];

        // Validator Setters
        $di->setters[\Ushahidi\App\Validator\Form\Stage\Update::class] = [
            'setFormRepo' => $di->lazyGet('repository.form'),
        ];
        $di->setters[\Ushahidi\App\Validator\Form\Role\Update::class] = [
            'setFormRepo' => $di->lazyGet('repository.form'),
            'setRoleRepo' => $di->lazyGet('repository.role'),
        ];
        $di->setters[\Ushahidi\App\Validator\Media\Create::class] = [
            'setMaxBytes' => $di->lazy(function () {
                return config('media.max_upload_bytes');
            }),
        ];
        $di->setters[\Ushahidi\App\Validator\CSV\Create::class] = [
            // @todo load from config
            'setMaxBytes' => '2048000',
        ];


        $di->set('validator.post.datetime', $di->lazyNew(\Ushahidi\App\Validator\Post\Datetime::class));
        $di->set('validator.post.decimal', $di->lazyNew(\Ushahidi\App\Validator\Post\Decimal::class));
        $di->set('validator.post.geometry', $di->lazyNew(\Ushahidi\App\Validator\Post\Geometry::class));
        $di->set('validator.post.int', $di->lazyNew(\Ushahidi\App\Validator\Post\Integer::class));
        $di->set('validator.post.link', $di->lazyNew(\Ushahidi\App\Validator\Post\Link::class));
        $di->set('validator.post.point', $di->lazyNew(\Ushahidi\App\Validator\Post\Point::class));
        $di->set('validator.post.relation', $di->lazyNew(\Ushahidi\App\Validator\Post\Relation::class));
        $di->set('validator.post.varchar', $di->lazyNew(\Ushahidi\App\Validator\Post\Varchar::class));
        $di->set('validator.post.markdown', $di->lazyNew(\Ushahidi\App\Validator\Post\Markdown::class));
        $di->set('validator.post.video', $di->lazyNew(\Ushahidi\App\Validator\Post\Video::class));
        $di->set('validator.post.title', $di->lazyNew(\Ushahidi\App\Validator\Post\Title::class));
        $di->set('validator.post.media', $di->lazyNew(\Ushahidi\App\Validator\Post\Media::class));
        $di->params[\Ushahidi\App\Validator\Post\Media::class] = [
            'media_repo' => $di->lazyGet('repository.media')
        ];
        $di->set('validator.post.tags', $di->lazyNew(\Ushahidi\App\Validator\Post\Tags::class));
        $di->params[\Ushahidi\App\Validator\Post\Tags::class] = [
            'tags_repo' => $di->lazyGet('repository.tag')
        ];


        $di->set('validator.post.value_factory', $di->lazyNew(\Ushahidi\App\Validator\Post\ValueFactory::class));
        $di->params[\Ushahidi\App\Validator\Post\ValueFactory::class] = [
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

        $di->params[\Ushahidi\App\Validator\Post\Relation::class] = [
            'repo' => $di->lazyGet('repository.post')
        ];

        $di->set('transformer.csv', $di->lazyNew(\Ushahidi\App\V3\Transformer\CSVPostTransformer::class));
        // Post repo for mapping transformer
        $di->setters[\Ushahidi\App\V3\Transformer\CSVPostTransformer::class]['setRepo'] =
            $di->lazyGet('repository.post');

        // Event listener for the Set repo
        $di->setters[\Ushahidi\App\Repository\SetRepository::class]['setEvent'] = 'PostSetEvent';

        $di->setters[\Ushahidi\App\Repository\SetRepository::class]['setListener'] =
            $di->lazyNew(\Ushahidi\App\Listener\PostSetListener::class);

        // NotificationQueue repo for Set listener
        $di->setters[\Ushahidi\App\Listener\PostSetListener::class]['setRepo'] =
            $di->lazyGet('repository.notification.queue');

        // Event listener for the Post repo
        $di->setters[\Ushahidi\App\Repository\PostRepository::class]['setEvent'] = 'PostCreateEvent';
        $di->setters[\Ushahidi\App\Repository\PostRepository::class]['setListener'] =
            $di->lazyNew(\Ushahidi\App\Listener\PostListener::class);

        // WebhookJob repo for Post listener
        $di->setters[\Ushahidi\App\Listener\PostListener::class]['setRepo'] =
            $di->lazyGet('repository.webhook.job');

        // Webhook repo for Post listener
        $di->setters[\Ushahidi\App\Listener\PostListener::class]['setWebhookRepo'] =
            $di->lazyGet('repository.webhook');

        // Add Intercom Listener to Config
        $di->setters[\Ushahidi\App\Repository\ConfigRepository::class]['setEvent'] = 'ConfigUpdateEvent';
        $di->setters[\Ushahidi\App\Repository\ConfigRepository::class]['setListener'] =
            $di->lazyNew(\Ushahidi\App\Listener\IntercomCompanyListener::class);

        // Add Intercom Listener to Form
        $di->setters[\Ushahidi\App\Repository\FormRepository::class]['setEvent'] = 'FormUpdateEvent';
        $di->setters[\Ushahidi\App\Repository\FormRepository::class]['setListener'] =
            $di->lazyNew(\Ushahidi\App\Listener\IntercomCompanyListener::class);

        // Add Intercom Listener to User
        $di->setters[\Ushahidi\App\Repository\UserRepository::class]['setEvent'] = 'UserGetAllEvent';
        $di->setters[\Ushahidi\App\Repository\UserRepository::class]['setListener'] =
            $di->lazyNew(\Ushahidi\App\Listener\IntercomAdminListener::class);

        // Add Lock Listener
        $di->setters[\Ushahidi\App\Repository\Post\LockRepository::class]['setEvent'] = 'LockBroken';

        $di->setters[\Ushahidi\Core\Usecase\ImportUsecase::class]['setEvent'] = 'ImportPosts';
        $di->setters[\Ushahidi\Core\Usecase\ImportUsecase::class]['setListener'] =
            $di->lazyNew(\Ushahidi\App\Listener\Import::class);
        /**
         * HXL block
         */

        // generic authorizer for hxl
        $di->set('authorizer.hxl', $di->lazyNew('Ushahidi\Core\Tools\Authorizer\HXLAuthorizer'));

        $di->params[Ushahidi\Factory\AuthorizerFactory::class]['map']['hxl'] =
            $di->lazyGet('authorizer.hxl');

        // hxl meta_data
        $di->set('repository.hxl_meta_data', $di->lazyNew(\Ushahidi\App\Repository\HXL\HXLMetadataRepository::class));
        $di->set('formatter.entity.hxl_meta_data', $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLMetadata::class));
        $di->set(
            'authorizer.hxl.meta_data',
            $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\HXLMetadataAuthorizer::class)
        );

        $di->setters[\Ushahidi\App\Formatter\HXL\HXLMetadata::class]['setAuth']
            = $di->lazyGet("authorizer.hxl.meta_data");
        $di->params[\Ushahidi\Factory\UsecaseFactory::class]['map']['hxl_meta_data'] = [
            'create' => $di->newFactory(\Ushahidi\Core\Usecase\HXL\Metadata\Create::class),
        ];
        $di->params[\Ushahidi\Factory\AuthorizerFactory::class]['map']['hxl_meta_data'] =
            $di->lazyGet('authorizer.hxl.meta_data');
        $di->params[\Ushahidi\Factory\RepositoryFactory::class]['map']['hxl_meta_data'] =
            $di->lazyGet('repository.hxl_meta_data');
        $di->params[\Ushahidi\Factory\FormatterFactory::class]['map']['hxl_meta_data'] =
            $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLMetadata::class);
        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['hxl_meta_data'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\HXL\Metadata\Create::class),
        ];

        $di->params[\Ushahidi\App\Validator\ExportJob\Update::class] = [
            'repo' => $di->lazyGet('repository.export_job'),
            'hxl_meta_data_repo' => $di->lazyGet('repository.hxl_meta_data'),
            'user_repo' => $di->lazyGet('repository.user'),
        ];

        $di->params[\Ushahidi\App\Validator\HXL\Metadata\Create::class] = [
            'repo' => $di->lazyGet('repository.hxl_meta_data'),
            'license_repo' => $di->lazyGet('repository.hxl_license'),
            'user_repo' => $di->lazyGet('repository.user'),
        ];

        $di->set(
            'formatter.entity.form_attribute_hxl_attribute_tag',
            $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class)
        );
        $di->set(
            'authorizer.hxl.form_attribute_hxl_attribute_tag',
            $di->lazyNew('Ushahidi\Core\Tools\Authorizer\HXLAuthorizer')
        );
        $di->setters[\Ushahidi\App\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class]['setAuth']
            = $di->lazyGet("authorizer.hxl");
        $di->params[\Ushahidi\Factory\AuthorizerFactory::class]['map']['form_attribute_hxl_attribute_tag'] =
            $di->lazyGet('authorizer.hxl.form_attribute_hxl_attribute_tag');
        $di->params[\Ushahidi\Factory\RepositoryFactory::class]['map']['form_attribute_hxl_attribute_tag'] =
            $di->lazyGet('repository.form_attribute_hxl_attribute_tag');
        $di->params[\Ushahidi\Factory\FormatterFactory::class]['map']['form_attribute_hxl_attribute_tag'] =
            $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class);
        $di->setters[\Ushahidi\App\Formatter\HXL\HXLFormAttributeHXLAttributeTagFormatter::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        $di->params[\Ushahidi\Factory\ValidatorFactory::class]['map']['form_attribute_hxl_attribute_tag'] = [
            'create' => $di->lazyNew(\Ushahidi\App\Validator\HXL\HXLFormAttributeHXLAttributeTag\Create::class),
        ];

        $di->params[\Ushahidi\App\Validator\HXL\HXLFormAttributeHXLAttributeTag\Create::class] = [
            'repo' => $di->lazyGet('repository.form_attribute_hxl_attribute_tag'),
            'export_job_repo' => $di->lazyGet('repository.export_job'),
            'hxl_attribute_repo' => $di->lazyGet('repository.hxl_attribute'), //todo
            'hxl_tag_repo' => $di->lazyGet('repository.hxl_tag'),
            'form_attribute_repo' => $di->lazyGet('repository.form_attribute'),
        ];
        $di->setters[\Ushahidi\Core\Usecase\Export\Job\CreateJob::class]['setCreateHXLHeadingRowUsecase']
            = $di->lazy(function () {
                return service('factory.usecase')->get('form_attribute_hxl_attribute_tag', 'create');
            });

        $di->set(
            'repository.form_attribute_hxl_attribute_tag',
            $di->lazyNew(\Ushahidi\App\Repository\HXL\HXLFormAttributeHXLAttributeTagRepository::class)
        );
        $di->setters[\Ushahidi\Core\Usecase\Post\Export::class]['setFormAttributeRepository'] =
            $di->lazyGet('repository.form_attribute');

        // hxl attributes
        $di->set('repository.hxl_attribute', $di->lazyNew(\Ushahidi\App\Repository\HXL\HXLAttributeRepository::class));
        $di->params[\Ushahidi\Factory\RepositoryFactory::class]['map']['hxl_attributess'] =
            $di->lazyGet('repository.hxl_attribute');


        // hxl licenses
        $di->set('repository.hxl_license', $di->lazyNew(\Ushahidi\App\Repository\HXL\HXLLicenseRepository::class));

        $di->set('formatter.entity.hxl_license', $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLLicense::class));

        $di->params[\Ushahidi\Factory\AuthorizerFactory::class]['map']['hxl_licenses'] =
            $di->lazyGet('authorizer.hxl');
        $di->params[\Ushahidi\Factory\RepositoryFactory::class]['map']['hxl_licenses'] =
            $di->lazyGet('repository.hxl_license');
        $di->params[\Ushahidi\Factory\FormatterFactory::class]['map']['hxl_licenses'] =
            $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLLicense::class);
        $di->setters[\Ushahidi\App\Formatter\HXL\HXLLicense::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        // hxl tags
        $di->set('repository.hxl_tag', $di->lazyNew(\Ushahidi\App\Repository\HXL\HXLTagRepository::class));
        $di->set('formatter.entity.hxl_tag', $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLTag::class));
        $di->params[\Ushahidi\Factory\AuthorizerFactory::class]['map']['hxl_tags'] =
            $di->lazyGet('authorizer.hxl');
        $di->params[\Ushahidi\Factory\RepositoryFactory::class]['map']['hxl_tags'] =
            $di->lazyGet('repository.hxl_tag');
        $di->params[\Ushahidi\Factory\FormatterFactory::class]['map']['hxl_tags'] =
            $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLTag::class);
        $di->setters[\Ushahidi\App\Formatter\HXL\HXLTag::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        // repositories for Ushahidi\Core\Usecase\HXL\SendHXLUsecase
        $di->setters[\Ushahidi\Core\Usecase\HXL\SendHXLUsecase::class]['setExportJobRepository'] =
            $di->lazyGet('repository.export_job');
        $di->setters[\Ushahidi\Core\Usecase\HXL\SendHXLUsecase::class]['setUserHXLSettingsRepository'] =
            $di->lazyGet('repository.user_setting');
        $di->setters[\Ushahidi\Core\Usecase\HXL\SendHXLUsecase::class]['setHXLMetadataRepository'] =
            $di->lazyGet('repository.hxl_meta_data');
        $di->setters[\Ushahidi\Core\Usecase\HXL\SendHXLUsecase::class]['setHXLLicenseRepository'] =
            $di->lazyGet('repository.hxl_license');
        $di->setters[\Ushahidi\Core\Usecase\HXL\SendHXLUsecase::class]['setHXLFormAttributeHXLAttributeTagRepository'] =
            $di->lazyGet('repository.form_attribute_hxl_attribute_tag');

        // Add usecase for hxl_send

        $di->params[\Ushahidi\Factory\RepositoryFactory::class]['map']['hxl_send'] =
            $di->lazyGet('repository.hxl_tag'); //FIXME
        $di->params[\Ushahidi\Factory\UsecaseFactory::class]['map']['hxl_send'] = [
            'send' => $di->lazyNew(\Ushahidi\Core\Usecase\HXL\SendHXLUsecase::class),
        ];

        $di->params[\Ushahidi\Factory\FormatterFactory::class]['map']['hxl_send'] =
            $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLSend::class);
        $di->params[\Ushahidi\Factory\AuthorizerFactory::class]['map']['hxl_send'] =
            $di->lazyGet('authorizer.hxl');
        // add organisations
        $di->params[\Ushahidi\Factory\UsecaseFactory::class]['map']['hxl_organisations'] = [
            'search' => $di->newFactory(\Ushahidi\Core\Usecase\HXL\Organisations\GetByUser::class),
        ];
        $di->params[\Ushahidi\Factory\FormatterFactory::class]['map']['hxl_organisations'] =
            $di->lazyNew(\Ushahidi\App\Formatter\HXL\HXLOrganisations::class);
        $di->params[\Ushahidi\Factory\AuthorizerFactory::class]['map']['hxl_organisations'] =
            $di->lazyGet('authorizer.hxl');

        $di->setters[\Ushahidi\App\Formatter\HXL\HXLOrganisations::class]['setAuth'] =
            $di->lazyGet("authorizer.hxl");

        $di->setters[\Ushahidi\Core\Usecase\HXL\Organisations\GetByUser::class]['setUserHXLSettingsRepository'] =
            $di->lazyGet('repository.user_setting');
        $di->setters[\Ushahidi\Core\Usecase\HXL\Organisations\GetByUser::class]['setRepository'] =
            null;
        $di->params[\Ushahidi\Factory\RepositoryFactory::class]['map']['hxl_organisations'] =
            $di->lazyGet('repository.hxl_tag'); //FIXME

        // Authorizer
        $di->set('authorizer.config', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\ConfigAuthorizer::class));
        $di->set('authorizer.console', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\ConsoleAuthorizer::class));
        $di->set(
            'authorizer.dataprovider',
            $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\DataProviderAuthorizer::class)
        );
        $di->set('authorizer.form', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\FormAuthorizer::class, [
            'form_repo' => $di->lazyGet('repository.form'),
        ]));
        $di->params[\v5\Policies\SurveyPolicy::class] = [
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->set(
            'authorizer.form_attribute',
            $di->lazyNew(
                \Ushahidi\Core\Tools\Authorizer\FormAttributeAuthorizer::class,
                [
                'stage_repo' => $di->lazyGet('repository.form_stage'),
                'stage_auth' => $di->lazyGet('authorizer.form_stage'),
                ]
            )
        );
        $di->set('authorizer.form_role', $di->lazyNew(
            \Ushahidi\Core\Tools\Authorizer\FormRoleAuthorizer::class,
            [
                'form_repo' => $di->lazyGet('repository.form'),
                'form_auth' => $di->lazyGet('authorizer.form'),
            ]
        ));
        $di->set(
            'authorizer.form_stage',
            $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\FormStageAuthorizer::class, [
                'form_repo' => $di->lazyGet('repository.form'),
                'form_auth' => $di->lazyGet('authorizer.form'),
            ])
        );
        $di->set('authorizer.form_contact', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\FormContactAuthorizer::class, [
            'form_repo' => $di->lazyGet('repository.form'),
            'form_auth' => $di->lazyGet('authorizer.form'),
        ]));
        $di->set('authorizer.form_stats', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\FormStatsAuthorizer::class));
        $di->set('authorizer.user', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\UserAuthorizer::class));
        $di->set('authorizer.user_setting', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\UserSettingAuthorizer::class));
        $di->set('authorizer.layer', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\LayerAuthorizer::class));
        $di->set('authorizer.media', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\MediaAuthorizer::class));
        $di->set('authorizer.message', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\MessageAuthorizer::class));
        $di->set('authorizer.tag', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\TagAuthorizer::class));
        $di->set('authorizer.savedsearch', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\SetAuthorizer::class));
        $di->set('authorizer.set', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\SetAuthorizer::class));
        $di->set(
            'authorizer.notification',
            $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\NotificationAuthorizer::class)
        );
        $di->set('authorizer.webhook', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\WebhookAuthorizer::class));
        $di->set('authorizer.apikey', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\ApiKeyAuthorizer::class));
        $di->set('authorizer.contact', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\ContactAuthorizer::class));
        $di->set('authorizer.csv', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\CSVAuthorizer::class));
        $di->set('authorizer.role', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\RoleAuthorizer::class));
        $di->set('authorizer.permission', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\PermissionAuthorizer::class));
        $di->set('authorizer.post', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\PostAuthorizer::class));
        $di->set('authorizer.post_lock', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\PostAuthorizer::class));
        $di->set('authorizer.tos', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\TosAuthorizer::class));
        $di->set('authorizer.external_auth', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\ExternalAuthorizer::class));
        $di->set('authorizer.export_job', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\ExportJobAuthorizer::class));
        $di->params[\Ushahidi\Core\Tools\Authorizer\PostAuthorizer::class] = [
            'post_repo' => $di->lazyGet('repository.post'),
            'form_repo' => $di->lazyGet('repository.form'),
        ];
        $di->params[\Ushahidi\Core\Tools\Authorizer\TagAuthorizer::class] = [
            'tag_repo' => $di->lazyGet('repository.tag'),
        ];

        $di->set('authorizer.country_code', $di->lazyNew(\Ushahidi\Core\Tools\Authorizer\CountryCodeAuthorizer::class));

        //FIXME
        $di->set('repository.hxl_organisations', $di->lazyNew(\Ushahidi\App\Repository\HXL\HXLTagRepository::class));

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
