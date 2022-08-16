<?php

namespace Ushahidi\Core;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Ushahidi\Core\Usecase;

class CoreConfig extends ContainerConfig
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
        // When adding services that are private to a plugin, define them with a
        // `namespace.`, such as `acme.tool.hash.magic`.

        // Validators are used to parse **and** verify input data used for write operations.
        $di->set('factory.validator', $di->lazyNew(\Ushahidi\App\V3\Factory\ValidatorFactory::class));

        // Implemented validators will be mapped to resources and actions.
        $di->params[\Ushahidi\App\V3\Factory\ValidatorFactory::class]['map'] = [];

        // Authorizers are used to check if the accessing user has permission to use an action.
        $di->set('factory.authorizer', $di->lazyNew(\Ushahidi\App\V3\Factory\AuthorizerFactory::class));

        // Authorizers are shared, so mapping is done with service names.
        $di->params[\Ushahidi\App\V3\Factory\AuthorizerFactory::class]['map'] = [
            'config'               => $di->lazyGet('authorizer.config'),
            'dataproviders'        => $di->lazyGet('authorizer.dataprovider'),
            'export_jobs'          => $di->lazyGet('authorizer.export_job'),
            'country_codes'        => $di->lazyGet('authorizer.country_code'),
            'external_auth'        => $di->lazyGet('authorizer.external_auth'),
            'forms'                => $di->lazyGet('authorizer.form'),
            'form_contacts'        => $di->lazyGet('authorizer.form_contact'),
            'form_attributes'      => $di->lazyGet('authorizer.form_attribute'),
            'form_roles'           => $di->lazyGet('authorizer.form_role'),
            'form_stages'          => $di->lazyGet('authorizer.form_stage'),
            'form_stats'           => $di->lazyGet('authorizer.form_stats'),
            'layers'               => $di->lazyGet('authorizer.layer'),
            'media'                => $di->lazyGet('authorizer.media'),
            'messages'             => $di->lazyGet('authorizer.message'),
            'posts'                => $di->lazyGet('authorizer.post'),
            'posts_lock'           => $di->lazyGet('authorizer.post_lock'),
            'tags'                 => $di->lazyGet('authorizer.tag'),
            'sets'                 => $di->lazyGet('authorizer.set'),
            'sets_posts'           => $di->lazyGet('authorizer.post'),
            'savedsearches'        => $di->lazyGet('authorizer.savedsearch'),
            'users'                => $di->lazyGet('authorizer.user'),
            'user_settings'        => $di->lazyGet('authorizer.user_setting'),
            'notifications'        => $di->lazyGet('authorizer.notification'),
            'webhooks'             => $di->lazyGet('authorizer.webhook'),
            'apikeys'              => $di->lazyGet('authorizer.apikey'),
            'contacts'             => $di->lazyGet('authorizer.contact'),
            'csv'                  => $di->lazyGet('authorizer.csv'),
            'roles'                => $di->lazyGet('authorizer.role'),
            'permissions'          => $di->lazyGet('authorizer.permission'),
            'posts_export'         => $di->lazyGet('authorizer.post'),
            'tos'                   => $di->lazyGet('authorizer.tos'),
        ];

        // Repositories are used for storage and retrieval of records.
        $di->set('factory.repository', $di->lazyNew(\Ushahidi\App\V3\Factory\RepositoryFactory::class));

        // Repositories are shared, so mapping is done with service names.
        $di->params[\Ushahidi\App\V3\Factory\RepositoryFactory::class]['map'] = [
            'config'               => $di->lazyGet('repository.config'),
            'country_codes'        => $di->lazyGet('repository.country_code'),
            'export_jobs'          => $di->lazyGet('repository.export_job'),
            'dataproviders'        => $di->lazyGet('repository.dataprovider'),
            'targeted_survey_states'   => $di->lazyGet('repository.targeted_survey_state'),
            'forms'                => $di->lazyGet('repository.form'),
            'form_attributes'      => $di->lazyGet('repository.form_attribute'),
            'form_contacts'        => $di->lazyGet('repository.form_contact'),
            'form_stats'           => $di->lazyGet('repository.form_stats'),
            'form_roles'           => $di->lazyGet('repository.form_role'),
            'form_stages'          => $di->lazyGet('repository.form_stage'),
            'layers'               => $di->lazyGet('repository.layer'),
            'media'                => $di->lazyGet('repository.media'),
            'messages'             => $di->lazyGet('repository.message'),
            'posts'                => $di->lazyGet('repository.post'),
            'posts_lock'           => $di->lazyGet('repository.post_lock'),
            'tags'                 => $di->lazyGet('repository.tag'),
            'sets'                 => $di->lazyGet('repository.set'),
            'sets_posts'           => $di->lazyGet('repository.post'),
            'savedsearches'        => $di->lazyGet('repository.savedsearch'),
            'users'                => $di->lazyGet('repository.user'),
            'user_settings'        => $di->lazyGet('repository.user_setting'),
            'notifications'        => $di->lazyGet('repository.notification'),
            'webhooks'             => $di->lazyGet('repository.webhook'),
            'apikeys'              => $di->lazyGet('repository.apikey'),
            'contacts'             => $di->lazyGet('repository.contact'),
            'csv'                  => $di->lazyGet('repository.csv'),
            'roles'                => $di->lazyGet('repository.role'),
            'permissions'          => $di->lazyGet('repository.permission'),
            'posts_export'         => $di->lazyGet('repository.export_batch'),
            'tos'                  => $di->lazyGet('repository.tos'),
        ];

        // Formatters are used for to prepare the output of records. Actions that return
        // multiple results use collection formatters for recursion.
        $di->set('factory.formatter', $di->lazyNew(\Ushahidi\App\V3\Factory\FormatterFactory::class));

        // Implemented collection formatter will register as the factory.
        $di->params[\Ushahidi\App\V3\Factory\FormatterFactory::class]['factory'] = null;

        // Formatters used on collections of records are run recursively. This expectation
        // is mapped by actions that return collections.
        $di->params[\Ushahidi\App\V3\Factory\FormatterFactory::class]['collections'] = [
            'search' => true,
            'update_collection' => true
        ];

        // Data transfer objects are used to carry complex search filters between collaborators.
        $di->set('factory.data', $di->lazyNew(\Ushahidi\App\V3\Factory\DataFactory::class));

        // Usecases that perform searches are the most typical usage of data objects.
        $di->params[\Ushahidi\App\V3\Factory\DataFactory::class]['actions'] = [
            'search' => $di->lazyNew(\Ushahidi\Core\Tool\SearchData::class),
            'stats'  => $di->lazyNew(\Ushahidi\Core\Tool\SearchData::class),
            'export'  => $di->lazyNew(\Ushahidi\Core\Tool\SearchData::class),
        ];

        // Use cases are used to join multiple collaborators together for a single interaction.
        $di->set('factory.usecase', $di->lazyNew(\Ushahidi\App\V3\Factory\UsecaseFactory::class));
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class] = [
            'authorizers'  => $di->lazyGet('factory.authorizer'),
            'repositories' => $di->lazyGet('factory.repository'),
            'formatters'   => $di->lazyGet('factory.formatter'),
            'validators'   => $di->lazyGet('factory.validator'),
            'data'         => $di->lazyGet('factory.data'),
        ];

        // Each of the actions follows a standard sequence of events and is simply constructed
        // with a unique set of collaborators that follow specific interfaces.
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['actions'] = [
            'create' => $di->newFactory(Usecase\CreateUsecase::class),
            'read'   => $di->newFactory(Usecase\ReadUsecase::class),
            'update' => $di->newFactory(Usecase\UpdateUsecase::class),
            'delete' => $di->newFactory(Usecase\DeleteUsecase::class),
            'search' => $di->newFactory(Usecase\SearchUsecase::class),
            'options' => $di->newFactory(Usecase\OptionsUsecase::class),
        ];

        // It is also possible to overload usecases by setting a specific resource and action.
        // The same collaborator mapping will be applied by action as with default use cases.
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map'] = [];

        // Config does not allow ordering or sorting, because of its simple key/value nature.
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['config'] = [
            'search' => $di->newFactory(Usecase\Config\SearchConfig::class),
        ];

        // Form sub-endpoints must verify that the form exists before anything else.
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['form_attributes'] = [
            'create'  => $di->lazyNew(Usecase\Form\CreateFormAttribute::class),
            'read'    => $di->lazyNew(Usecase\Form\ReadFormAttribute::class),
            'update'  => $di->lazyNew(Usecase\Form\UpdateFormAttribute::class),
            'delete'  => $di->lazyNew(Usecase\Form\DeleteFormAttribute::class),
            'search'  => $di->lazyNew(Usecase\Form\SearchFormAttribute::class),
        ];
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['form_roles'] = [
            'update_collection'  => $di->lazyNew(Usecase\Form\UpdateFormRole::class),
            'search'  => $di->lazyNew(Usecase\Form\SearchFormRole::class),
        ];

        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['form_contacts'] = [
            'create'  => $di->lazyNew(Usecase\Form\CreateFormContact::class),
            'read'    => $di->lazyNew(Usecase\Form\ReadFormContact::class),
            //'update'  => $di->lazyNew(Usecase\Form\UpdateFormContact::class),
            // 'delete'  => $di->lazyNew(Usecase\Form\DeleteFormContact::class),
            'search'  => $di->lazyNew(Usecase\Form\SearchFormContact::class),
        ];

        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['form_stats'] = [
            'search'  => $di->lazyNew(Usecase\Form\SearchFormStats::class),
        ];

        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['form_stages'] = [
            'create'  => $di->lazyNew(Usecase\Form\CreateFormStage::class),
            'read'    => $di->lazyNew(Usecase\Form\ReadFormStage::class),
            'update'  => $di->lazyNew(Usecase\Form\UpdateFormStage::class),
            'delete'  => $di->lazyNew(Usecase\Form\DeleteFormStage::class),
            'search'  => $di->lazyNew(Usecase\Form\SearchFormStage::class),
        ];

        // Media create requires file uploading as part of the payload.
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['media'] = [
            'create' => $di->lazyNew(Usecase\Media\CreateMedia::class),
        ];
        $di->setters[Usecase\Media\CreateMedia::class]['setUploader'] = $di->lazyGet('tool.uploader');

        // CSV requires file upload
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['csv'] = [
            'create' => $di->lazyNew(Usecase\CSV\CreateCSVUsecase::class),
            'read'    => $di->lazyNew(Usecase\ReadUsecase::class),
            'delete' => $di->lazyNew(Usecase\CSV\DeleteCSVUsecase::class),
        ];

        $di->setters[Usecase\CSV\CreateCSVUsecase::class]['setUploader'] = $di->lazyGet('tool.uploader');
        $di->setters[Usecase\CSV\CreateCSVUsecase::class]['setReaderFactory']
            = $di->lazyGet('csv.reader_factory');
        $di->setters[Usecase\CSV\DeleteCSVUsecase::class]['setUploader'] = $di->lazyGet('tool.uploader');

        // Message update requires extra validation of message direction+status.
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['messages'] = [
            'create' => $di->lazyNew(Usecase\Message\CreateMessage::class),
            'update' => $di->lazyNew(Usecase\Message\UpdateMessage::class),
            'receive' => $di->newFactory(Usecase\Message\ReceiveMessage::class),
        ];
        // Message receive requires extra repos
        $di->setters[Usecase\Message\ReceiveMessage::class]['setContactRepository']
            = $di->lazyGet('repository.contact');
        $di->setters[Usecase\Message\ReceiveMessage::class]['setContactValidator']
            = $di->lazyGet('validator.contact.receive');

        // Add custom usecases for posts
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['posts'] = [
            'create'          => $di->lazyNew(Usecase\Post\CreatePost::class),
            'read'            => $di->lazyNew(Usecase\Post\ReadPost::class),
            'update'          => $di->lazyNew(Usecase\Post\UpdatePost::class),
            'webhook-update'  => $di->lazyNew(Usecase\Post\WebhookUpdatePost::class),
            'delete'          => $di->lazyNew(Usecase\Post\DeletePost::class),
            'search'          => $di->lazyNew(Usecase\Post\SearchPost::class),
            'stats'           => $di->lazyNew(Usecase\Post\StatsPost::class),
            'import'          => $di->lazyNew(Usecase\Post\ImportPost::class)
        ];
        // Add custom create usecase for notifications
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['notifications'] = [
            'create'  => $di->lazyNew(Usecase\Notification\CreateNotification::class)
        ];

        // Add custom create usecase for webhooks
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['webhooks'] = [
            'create'  => $di->lazyNew(Usecase\Webhook\CreateWebhook::class)
        ];

        // Add custom create usecase for export jobs
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['export_jobs'] = [
            'create'  => $di->lazyNew(Usecase\Export\Job\CreateJob::class),
            'post-count'  => $di->lazyNew(Usecase\Export\Job\PostCount::class)
        ];
        // Add custom create usecase for contacts
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['contacts'] = [
            'create'  => $di->lazyNew(Usecase\Contact\CreateContact::class)
        ];

        // Add custom create usecase for terms of service
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['tos'] = [
            'create' => $di->lazyNew(Usecase\Tos\CreateTos::class),
            'search' => $di->lazyNew(Usecase\Tos\SearchTos::class),
        ];

        // Add custom usecases for sets_posts
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['sets_posts'] = [
            'search' => $di->lazyNew(Usecase\Set\SearchSetPost::class),
            'create' => $di->lazyNew(Usecase\Set\CreateSetPost::class),
            'delete' => $di->lazyNew(Usecase\Set\DeleteSetPost::class),
            'read'   => $di->lazyNew(Usecase\Set\ReadSetPost::class),
        ];

        // Add custom useses for post_lock
        // Add usecase for posts_lock
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['posts_lock'] = [
            'create' => $di->lazyNew(Usecase\Post\CreatePostLock::class),
            'delete' => $di->lazyNew(Usecase\Post\DeletePostLock::class),
        ];

        $di->setters[Usecase\Post\Concerns\PostLock::class]['setPostRepository'] = $di->lazyGet('repository.post');

        // Add custom usecases for sets_posts
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['savedsearches'] = [
            'create' => $di->lazyNew(Usecase\Set\CreateSet::class),
        ];

        // Add custom usecases for sets_posts
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['sets'] = [
            'create' => $di->lazyNew(Usecase\Set\CreateSet::class),
        ];

        // Add usecase for posts_export
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['posts_export'] = [
            'export' => $di->lazyNew(Usecase\Post\ExportPost::class),
        ];


        // Set up traits for SetsPosts Usecases
        $di->setters[Usecase\Set\SetRepositoryTrait::class]['setSetRepository']
            = $di->lazyGet('repository.set');
        $di->setters[Usecase\Set\AuthorizeSet::class]['setSetAuthorizer']
            = $di->lazyGet('authorizer.set');

        // repositories for Ushahidi\Contracts\Repository\Usecase\Post\Export usecase
        $di->setters[Usecase\Post\ExportPost::class]['setExportJobRepository']
            = $di->lazyGet('repository.export_job');
        $di->setters[Usecase\Post\ExportPost::class]['setFormAttributeRepository']
            = $di->lazyGet('repository.form_attribute');
        $di->setters[Usecase\Post\ExportPost::class]['setPostExportRepository']
            = $di->lazyGet('repository.posts_export');
        $di->setters[Usecase\Post\ExportPost::class]['setHXLFromAttributeHxlAttributeTagRepo'] =
            $di->lazyGet('repository.form_attribute_hxl_attribute_tag');

        // User login is a custom read the uses authentication.
        $di->params[\Ushahidi\App\V3\Factory\UsecaseFactory::class]['map']['users'] = [
            'login'    => $di->lazyNew(Usecase\User\LoginUser::class),
            'register' => $di->lazyNew(Usecase\User\RegisterUser::class),
            'update'   => $di->lazyNew(Usecase\User\UpdateUser::class),
            'getresettoken' => $di->lazyNew(Usecase\User\GetResetToken::class),
            'passwordreset' => $di->lazyNew(Usecase\User\ResetUserPassword::class),
        ];
        $di->setters[Usecase\User\LoginUser::class]['setAuthenticator']
            = $di->lazyGet('tool.authenticator.password');
        $di->setters[Usecase\User\LoginUser::class]['setRateLimiter'] = $di->lazyGet('ratelimiter.login');

        $di->setters[Usecase\User\RegisterUser::class]['setRateLimiter']
            = $di->lazyGet('ratelimiter.register');

        $di->setters[Usecase\User\GetResetToken::class]['setMailer'] = $di->lazyGet('tool.mailer');

        // Traits
        $di->setters[\Ushahidi\Core\Concerns\UserContext::class]['setSession'] = $di->lazyGet('session');
        $di->setters[Usecase\Concerns\VerifyFormLoaded::class]['setFormRepository']
            = $di->lazyGet('repository.form');
        $di->setters[Usecase\Concerns\VerifyFormLoaded::class]['setFormContactRepository']
            = $di->lazyGet('repository.form_contact');
        $di->setters[Usecase\Concerns\VerifyStageLoaded::class]['setStageRepository']
            = $di->lazyGet('repository.form_stage');

        $di->setters[\Ushahidi\Core\Concerns\Event::class]['setEmitter'] = $di->lazyNew(\League\Event\Emitter::class);

        // Set post permissions instance
        $di->setters[\Ushahidi\Core\Tool\Permissions\InteractsWithPostPermissions::class]['setPostPermissions']
            = $di->lazyNew(\Ushahidi\Core\Tool\Permissions\PostPermissions::class);

        // Set form permissions instance
        $di->setters[\Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions::class]['setFormPermissions']
            = $di->lazyNew(\Ushahidi\Core\Tool\Permissions\FormPermissions::class);

        // Set ACL for ACL Trait
        $di->setters[\Ushahidi\Core\Concerns\Acl::class]['setAcl'] = $di->lazyGet('tool.acl');

        // Tools
        $di->set('tool.signer', $di->lazyNew(\Ushahidi\Core\Tool\Signer::class));
        $di->set('tool.verifier', $di->lazyNew(\Ushahidi\Core\Tool\Verifier::class, [
            'apiKeyRepo' => $di->lazyGet('repository.apikey')
        ]));
        $di->set('tool.uploader', $di->lazyNew(\Ushahidi\Core\Tool\Uploader::class));
        $di->params[\Ushahidi\Core\Tool\Uploader::class] = [
            'fs' => $di->lazyGet('tool.filesystem'),
            'multisite' => $di->lazyGet('multisite'),
        ];

        $di->set('tool.acl', $di->lazyNew(\Ushahidi\Core\Tool\Acl::class));
        $di->setters[\Ushahidi\Core\Tool\Acl::class]['setRoleRepo'] = $di->lazyGet('repository.role');

        $di->set('tool.hasher.password', $di->lazyNew(\Ushahidi\Core\Tool\Hasher\Password::class));
        $di->set('tool.authenticator.password', $di->lazyNew(\Ushahidi\Core\Tool\Authenticator\Password::class));

        $di->set('filereader.csv', $di->lazyNew(\Ushahidi\Core\Tool\FileReader\CSV::class));
        $di->setters[\Ushahidi\Core\Tool\FileReader\CSV::class]['setReaderFactory'] =
            $di->lazyGet('csv.reader_factory');

        $di->set('csv.reader_factory', $di->lazyNew(\Ushahidi\Core\Tool\FileReader\CSVReaderFactory::class));

        // Register filesystem adapter types

        // Set up register rate limiter
        // Rate limit storage cache
        $di->set('ratelimiter.cache', $di->lazy(function ($config) use ($di) {
            $cache = $config['cache'];

            // @todo we can't reconfigure this here. Need to move it elsewhere
            if ($cache === 'memcached') {
                return $di->newInstance(\Doctrine\Common\Cache\MemcachedCache::class);
            } elseif ($cache === 'filesystem') {
                return $di->newInstance(\Doctrine\Common\Cache\FilesystemCache::class);
            }

            // Fall back to using in-memory cache if none is configured
            return $di->newInstance('Doctrine\Common\Cache\ArrayCache');
        }, $di->lazyValue('ratelimiter.config')));

        // Rate limiter violation handler
        $di->setters[\BehEh\Flaps\Flap::class]['setViolationHandler'] =
            $di->lazyNew(\Ushahidi\Core\Tool\ThrottlingViolationHandler::class);

        $di->set('ratelimiter.register.flap', $di->lazyNew(
            \BehEh\Flaps\Flap::class,
            [
                'storage' => $di->lazyNew(
                    \BehEh\Flaps\Storage\DoctrineCacheAdapter::class,
                    [
                        'cache' => $di->lazyGet('ratelimiter.cache')
                    ]
                ),
                'name' => 'register'
            ]
        ));

        $di->set('ratelimiter.register.strategy', $di->lazyNew(
            \BehEh\Flaps\Throttling\LeakyBucketStrategy::class,
            [
                'requests' => 3,
                'timeSpan' => '1m'
            ]
        ));

        $di->set('ratelimiter.register', $di->lazyNew(
            \Ushahidi\Core\Tool\RateLimiter::class,
            [
                'flap' => $di->lazyGet('ratelimiter.register.flap'),
                'throttlingStrategy' => $di->lazyGet('ratelimiter.register.strategy'),
            ]
        ));

        // Set up login rate limiter
        $di->set('ratelimiter.login.flap', $di->lazyNew(
            \BehEh\Flaps\Flap::class,
            [
                'storage' => $di->lazyNew(
                    \BehEh\Flaps\Storage\DoctrineCacheAdapter::class,
                    [
                        'cache' => $di->lazyGet('ratelimiter.cache')
                    ]
                ),
                'name' => 'login'
            ]
        ));


        $di->set('ratelimiter.login.strategy', $di->lazyNew(
            \BehEh\Flaps\Throttling\LeakyBucketStrategy::class,
            [
                'requests' => 3,
                'timeSpan' => '1m'
            ]
        ));

        $di->set('ratelimiter.login', $di->lazyNew(
            \Ushahidi\Core\Tool\RateLimiter::class,
            [
                'flap' => $di->lazyGet('ratelimiter.login.flap'),
                'throttlingStrategy' => $di->lazyGet('ratelimiter.login.strategy'),
            ]
        ));

        // Defined memcached
        $di->set('memcached', $di->lazy(function ($config) {
            $memcached = new \Memcached;
            $memcached->addServer($config['memcached']['host'], $config['memcached']['port']);
            return $memcached;
        }, $di->lazyValue('ratelimiter.config')));

        $di->setters[\Doctrine\Common\Cache\MemcachedCache::class]['setMemcached'] = $di->lazyGet('memcached');
        $di->params[\Doctrine\Common\Cache\FilesystemCache::class]['directory'] = $di->lazy(function ($config) {
            return $config['filesystem']['directory'];
        }, $di->lazyValue('ratelimiter.config'));

        // Validation Trait
        // We're injecting via lazy so that we get a separate ValidationEngine for every validator
        // Rather than a shared engine as we would if we used lazyNew->set->lazyGet->
        $di->setters[\Ushahidi\Core\Concerns\ValidationEngine::class]['setValidation'] = $di->lazy(function () {
            // Create a new ValidationEngine
            return new \Ushahidi\Core\Tool\KohanaValidationEngine(app('translator'));
        });
    }
}
