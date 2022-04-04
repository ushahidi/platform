<?php

namespace Ushahidi\Core;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

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
        $di->set('factory.validator', $di->lazyNew('Ushahidi\Factory\ValidatorFactory'));

        // Implemented validators will be mapped to resources and actions.
        $di->params['Ushahidi\Factory\ValidatorFactory']['map'] = [];

        // Authorizers are used to check if the accessing user has permission to use an action.
        $di->set('factory.authorizer', $di->lazyNew('Ushahidi\Factory\AuthorizerFactory'));

        // Authorizers are shared, so mapping is done with service names.
        $di->params['Ushahidi\Factory\AuthorizerFactory']['map'] = [
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
            'tags'                 => $di->lazyGet('authorizer.tag'),
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
        $di->set('factory.repository', $di->lazyNew('Ushahidi\Factory\RepositoryFactory'));

        // Repositories are shared, so mapping is done with service names.
        $di->params['Ushahidi\Factory\RepositoryFactory']['map'] = [
            'config'               => $di->lazyGet('repository.config'),
            'country_codes'        => $di->lazyGet('repository.country_code'),
            'export_jobs'          => $di->lazyGet('repository.export_job'),
            'dataproviders'        => $di->lazyGet('repository.dataprovider'),
            'targeted_survey_states'   => $di->lazyGet('repository.targeted_survey_state'),
            'forms'                => $di->lazyGet('repository.form'),
            'form_attributes'      => $di->lazyGet('repository.form_attribute'),
            'form_contacts'      => $di->lazyGet('repository.form_contact'),
            'form_stats'      => $di->lazyGet('repository.form_stats'),
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
        $di->set('factory.formatter', $di->lazyNew('Ushahidi\Factory\FormatterFactory'));

        // Implemented collection formatter will register as the factory.
        $di->params['Ushahidi\Factory\FormatterFactory']['factory'] = null;

        // Formatters used on collections of records are run recursively. This expectation
        // is mapped by actions that return collections.
        $di->params['Ushahidi\Factory\FormatterFactory']['collections'] = [
            'search' => true,
            'update_collection' => true
        ];

        // Data transfer objects are used to carry complex search filters between collaborators.
        $di->set('factory.data', $di->lazyNew('Ushahidi\Factory\DataFactory'));

        // Usecases that perform searches are the most typical usage of data objects.
        $di->params['Ushahidi\Factory\DataFactory']['actions'] = [
            'search' => $di->lazyNew('Ushahidi\Core\Tool\SearchData'),
            'stats'  => $di->lazyNew('Ushahidi\Core\Tool\SearchData'),
            'export'  => $di->lazyNew('Ushahidi\Core\Tool\SearchData'),
        ];

        // Use cases are used to join multiple collaborators together for a single interaction.
        $di->set('factory.usecase', $di->lazyNew('Ushahidi\Factory\UsecaseFactory'));
        $di->params['Ushahidi\Factory\UsecaseFactory'] = [
            'authorizers'  => $di->lazyGet('factory.authorizer'),
            'repositories' => $di->lazyGet('factory.repository'),
            'formatters'   => $di->lazyGet('factory.formatter'),
            'validators'   => $di->lazyGet('factory.validator'),
            'data'         => $di->lazyGet('factory.data'),
        ];

        // Each of the actions follows a standard sequence of events and is simply constructed
        // with a unique set of collaborators that follow specific interfaces.
        $di->params['Ushahidi\Factory\UsecaseFactory']['actions'] = [
            'create' => $di->newFactory('Ushahidi\Core\Usecase\CreateUsecase'),
            'read'   => $di->newFactory('Ushahidi\Core\Usecase\ReadUsecase'),
            'update' => $di->newFactory('Ushahidi\Core\Usecase\UpdateUsecase'),
            'delete' => $di->newFactory('Ushahidi\Core\Usecase\DeleteUsecase'),
            'search' => $di->newFactory('Ushahidi\Core\Usecase\SearchUsecase'),
            'options' => $di->newFactory('Ushahidi\Core\Usecase\OptionsUsecase'),
        ];

        // It is also possible to overload usecases by setting a specific resource and action.
        // The same collaborator mapping will be applied by action as with default use cases.
        $di->params['Ushahidi\Factory\UsecaseFactory']['map'] = [];

        // Config does not allow ordering or sorting, because of its simple key/value nature.
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['config'] = [
            'search' => $di->newFactory('Ushahidi\Core\Usecase\Config\SearchConfig'),
        ];

        // Form sub-endpoints must verify that the form exists before anything else.
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_attributes'] = [
            'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\CreateFormAttribute'),
            'read'    => $di->lazyNew('Ushahidi\Core\Usecase\Form\ReadFormAttribute'),
            'update'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormAttribute'),
            'delete'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\DeleteFormAttribute'),
            'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormAttribute'),
        ];
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_roles'] = [
            'update_collection'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormRole'),
            'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormRole'),
        ];

        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_contacts'] = [
            'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\CreateFormContact'),
            'read'    => $di->lazyNew('Ushahidi\Core\Usecase\Form\ReadFormContact'),
            //'update'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormContact'),
            // 'delete'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\DeleteFormContact'),
            'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormContact'),
        ];

        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_stats'] = [
            'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormStats'),
        ];

        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['form_stages'] = [
            'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\CreateFormStage'),
            'read'    => $di->lazyNew('Ushahidi\Core\Usecase\Form\ReadFormStage'),
            'update'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\UpdateFormStage'),
            'delete'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\DeleteFormStage'),
            'search'  => $di->lazyNew('Ushahidi\Core\Usecase\Form\SearchFormStage'),
        ];

        // Media create requires file uploading as part of the payload.
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['media'] = [
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\Media\CreateMedia'),
        ];
        $di->setters['Ushahidi\Core\Usecase\Media\CreateMedia']['setUploader'] = $di->lazyGet('tool.uploader');

        // CSV requires file upload
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['csv'] = [
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\CSV\CreateCSVUsecase'),
            'read'    => $di->lazyNew('Ushahidi\Core\Usecase\ReadUsecase'),
            'delete' => $di->lazyNew('Ushahidi\Core\Usecase\CSV\DeleteCSVUsecase'),
        ];

        $di->setters['Ushahidi\Core\Usecase\CSV\CreateCSVUsecase']['setUploader'] = $di->lazyGet('tool.uploader');
        $di->setters['Ushahidi\Core\Usecase\CSV\CreateCSVUsecase']['setReaderFactory']
            = $di->lazyGet('csv.reader_factory');
        $di->setters['Ushahidi\Core\Usecase\CSV\DeleteCSVUsecase']['setUploader'] = $di->lazyGet('tool.uploader');

        // Message update requires extra validation of message direction+status.
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['messages'] = [
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\Message\CreateMessage'),
            'update' => $di->lazyNew('Ushahidi\Core\Usecase\Message\UpdateMessage'),
            'receive' => $di->newFactory('Ushahidi\Core\Usecase\Message\ReceiveMessage'),
        ];
        // Message receive requires extra repos
        $di->setters['Ushahidi\Core\Usecase\Message\ReceiveMessage']['setContactRepository']
            = $di->lazyGet('repository.contact');
        $di->setters['Ushahidi\Core\Usecase\Message\ReceiveMessage']['setContactValidator']
            = $di->lazyGet('validator.contact.receive');

        // Add custom usecases for posts
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['posts'] = [
            'create'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\CreatePost'),
            'read'            => $di->lazyNew('Ushahidi\Core\Usecase\Post\ReadPost'),
            'update'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\UpdatePost'),
            'webhook-update'  => $di->lazyNew('Ushahidi\Core\Usecase\Post\WebhookUpdatePost'),
            'delete'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\DeletePost'),
            'search'          => $di->lazyNew('Ushahidi\Core\Usecase\Post\SearchPost'),
            'stats'           => $di->lazyNew('Ushahidi\Core\Usecase\Post\StatsPost'),
            'import'          => $di->lazyNew('Ushahidi\Core\Usecase\ImportUsecase')
        ];
        // Add custom create usecase for notifications
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['notifications'] = [
            'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Notification\CreateNotification')
        ];

        // Add custom create usecase for webhooks
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['webhooks'] = [
            'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Webhook\CreateWebhook')
        ];

        // Add custom create usecase for export jobs
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['export_jobs'] = [
            'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Export\Job\CreateJob'),
            'post-count'  => $di->lazyNew('Ushahidi\Core\Usecase\Export\Job\PostCount')
        ];
        // Add custom create usecase for contacts
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['contacts'] = [
            'create'  => $di->lazyNew('Ushahidi\Core\Usecase\Contact\CreateContact')
        ];

        // Add custom create usecase for terms of service
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['tos'] = [
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\Tos\CreateTos'),
            'search' => $di->lazyNew('Ushahidi\Core\Usecase\Tos\SearchTos'),
        ];

        // Add custom usecases for sets_posts
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['sets_posts'] = [
            'search' => $di->lazyNew('Ushahidi\Core\Usecase\Set\SearchSetPost'),
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\Set\CreateSetPost'),
            'delete' => $di->lazyNew('Ushahidi\Core\Usecase\Set\DeleteSetPost'),
            'read'   => $di->lazyNew('Ushahidi\Core\Usecase\Set\ReadSetPost'),
        ];

        // Add custom useses for post_lock
        // Add usecase for posts_lock
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['posts_lock'] = [
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\Post\CreatePostLock'),
            'delete' => $di->lazyNew('Ushahidi\Core\Usecase\Post\DeletePostLock'),
        ];

        $di->setters['Ushahidi\Core\Usecase\Post\Concerns\PostLock']
            ['setPostRepository'] = $di->lazyGet('repository.post');

        // Add custom usecases for sets_posts
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['savedsearches'] = [
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\Set\CreateSet'),
        ];

        // Add custom usecases for sets_posts
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['sets'] = [
            'create' => $di->lazyNew('Ushahidi\Core\Usecase\Set\CreateSet'),
        ];

        // Add usecase for posts_export
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['posts_export'] = [
            'export' => $di->lazyNew('Ushahidi\Core\Usecase\Post\Export'),
        ];


        // Set up traits for SetsPosts Usecases
        $di->setters['Ushahidi\Core\Usecase\Set\SetRepositoryTrait']['setSetRepository']
            = $di->lazyGet('repository.set');
        $di->setters['Ushahidi\Core\Usecase\Set\AuthorizeSet']['setSetAuthorizer']
            = $di->lazyGet('authorizer.set');

        // repositories for Ushahidi\Contracts\Repository\Usecase\Post\Export usecase
        $di->setters['Ushahidi\Core\Usecase\Post\Export']['setExportJobRepository']
            = $di->lazyGet('repository.export_job');
        $di->setters['Ushahidi\Core\Usecase\Post\Export']['setFormAttributeRepository']
            = $di->lazyGet('repository.form_attribute');
        $di->setters['Ushahidi\Core\Usecase\Post\Export']['setPostExportRepository']
            = $di->lazyGet('repository.posts_export');

        $di->setters['Ushahidi\Core\Usecase\Post\Export']['setHXLFromAttributeHxlAttributeTagRepo'] =
            $di->lazyGet('repository.form_attribute_hxl_attribute_tag');

        // User login is a custom read the uses authentication.
        $di->params['Ushahidi\Factory\UsecaseFactory']['map']['users'] = [
            'login'    => $di->lazyNew('Ushahidi\Core\Usecase\User\LoginUser'),
            'register' => $di->lazyNew('Ushahidi\Core\Usecase\User\RegisterUser'),
            'update'   => $di->lazyNew('Ushahidi\Core\Usecase\User\UpdateUser'),
            'getresettoken' => $di->lazyNew('Ushahidi\Core\Usecase\User\GetResetToken'),
            'passwordreset' => $di->lazyNew('Ushahidi\Core\Usecase\User\ResetUserPassword'),
        ];
        $di->setters['Ushahidi\Core\Usecase\User\LoginUser']['setAuthenticator']
            = $di->lazyGet('tool.authenticator.password');
        $di->setters['Ushahidi\Core\Usecase\User\LoginUser']['setRateLimiter'] = $di->lazyGet('ratelimiter.login');

        $di->setters['Ushahidi\Core\Usecase\User\RegisterUser']['setRateLimiter']
            = $di->lazyGet('ratelimiter.register');

        $di->setters['Ushahidi\Core\Usecase\User\GetResetToken']['setMailer'] = $di->lazyGet('tool.mailer');

        // Traits
        $di->setters['Ushahidi\Core\Concerns\UserContext']['setSession'] = $di->lazyGet('session');
        $di->setters['Ushahidi\Core\Usecase\Concerns\VerifyFormLoaded']['setFormRepository']
            = $di->lazyGet('repository.form');
        $di->setters['Ushahidi\Core\Usecase\Concerns\VerifyFormLoaded']['setFormContactRepository']
            = $di->lazyGet('repository.form_contact');
        $di->setters['Ushahidi\Core\Usecase\Concerns\VerifyStageLoaded']['setStageRepository']
            = $di->lazyGet('repository.form_stage');

        $di->setters['Ushahidi\Core\Concerns\Event']['setEmitter'] = $di->lazyNew('League\Event\Emitter');
        // Set ACL for ACL Trait
        $di->setters['Ushahidi\Core\Tool\Permissions\AclTrait']['setAcl'] = $di->lazyGet('tool.acl');

        // Set post permissions instance
        $di->setters['Ushahidi\Core\Tool\Permissions\InteractsWithPostPermissions']['setPostPermissions']
            = $di->lazyNew(\Ushahidi\Core\Tool\Permissions\PostPermissions::class);

        // Set form permissions instance
        $di->setters['Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions']['setFormPermissions']
            = $di->lazyNew(\Ushahidi\Core\Tool\Permissions\FormPermissions::class);

        // Set ACL for ACL Trait
        $di->setters['Ushahidi\Core\Tool\Permissions\AclTrait']['setAcl'] = $di->lazyGet('tool.acl');

        // Tools
        $di->set('tool.signer', $di->lazyNew('Ushahidi\Core\Tool\Signer'));
        $di->set('tool.verifier', $di->lazyNew('Ushahidi\Core\Tool\Verifier', [
            'apiKeyRepo' => $di->lazyGet('repository.apikey')
        ]));
        $di->set('tool.uploader', $di->lazyNew('Ushahidi\Core\Tool\Uploader'));
        $di->params['Ushahidi\Core\Tool\Uploader'] = [
            'fs' => $di->lazyGet('tool.filesystem'),
            'multisite' => $di->lazyGet('multisite'),
        ];

        $di->set('tool.acl', $di->lazyNew(\Ushahidi\Core\Tool\Permissions\Acl::class));
        $di->setters[\Ushahidi\Core\Tool\Permissions\Acl::class]['setRoleRepo'] = $di->lazyGet('repository.role');

        $di->set('tool.hasher.password', $di->lazyNew(\Ushahidi\Core\Tool\Hasher\Password::class));
        $di->set('tool.authenticator.password', $di->lazyNew(\Ushahidi\Core\Tool\Authenticator\Password::class));

        $di->set('filereader.csv', $di->lazyNew(\Ushahidi\Core\Tool\FileReader\CSV::class));
        $di->setters[\Ushahidi\Core\Tool\FileReader\CSV::class]['setReaderFactory'] =
            $di->lazyGet('csv.reader_factory');

        $di->set('csv.reader_factory', $di->lazyNew(\Ushahidi\Core\Tool\FileReader\CSVReaderFactory::class));

        // Register filesystem adapter types

        // Set up register rate limiter
        $di->set('ratelimiter.register.flap', $di->lazyNew(
            'BehEh\Flaps\Flap',
            [
                'storage' => $di->lazyNew(
                    'BehEh\Flaps\Storage\DoctrineCacheAdapter',
                    [
                        'cache' => $di->lazyGet('ratelimiter.cache')
                    ]
                ),
                'name' => 'register'
            ]
        ));

        $di->set('ratelimiter.register.strategy', $di->lazyNew(
            'BehEh\Flaps\Throttling\LeakyBucketStrategy',
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
            'BehEh\Flaps\Flap',
            [
                'storage' => $di->lazyNew(
                    'BehEh\Flaps\Storage\DoctrineCacheAdapter',
                    [
                        'cache' => $di->lazyGet('ratelimiter.cache')
                    ]
                ),
                'name' => 'login'
            ]
        ));


        $di->set('ratelimiter.login.strategy', $di->lazyNew(
            'BehEh\Flaps\Throttling\LeakyBucketStrategy',
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

        $di->setters['Doctrine\Common\Cache\MemcachedCache']['setMemcached'] = $di->lazyGet('memcached');
        $di->params['Doctrine\Common\Cache\FilesystemCache']['directory'] = $di->lazy(function ($config) {
            return $config['filesystem']['directory'];
        }, $di->lazyValue('ratelimiter.config'));

        // Rate limit storage cache
        $di->set('ratelimiter.cache', $di->lazy(function ($config) use ($di) {
            $cache = $config['cache'];

            // @todo we can't reconfigure this here. Need to move it elsewhere
            if ($cache === 'memcached') {
                return $di->newInstance('Doctrine\Common\Cache\MemcachedCache');
            } elseif ($cache === 'filesystem') {
                return $di->newInstance('Doctrine\Common\Cache\FilesystemCache');
            }

            // Fall back to using in-memory cache if none is configured
            return $di->newInstance('Doctrine\Common\Cache\ArrayCache');
        }, $di->lazyValue('ratelimiter.config')));

        // Rate limiter violation handler
        $di->setters['BehEh\Flaps\Flap']['setViolationHandler'] =
            $di->lazyNew(\Ushahidi\Core\Tool\ThrottlingViolationHandler::class);

        // Validation Trait
        // We're injecting via lazy so that we get a separate ValidationEngine for every validator
        // Rather than a shared engine as we would if we used lazyNew->set->lazyGet->
        $di->setters['Ushahidi\Core\Tool\ValidationEngineTrait']['setValidation'] = $di->lazy(function () {
            // Create a new ValidationEngine
            return new \Ushahidi\Core\Tool\KohanaValidationEngine(app('translator'));
        });
    }
}
