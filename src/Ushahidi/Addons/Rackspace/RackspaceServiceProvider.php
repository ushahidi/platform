<?php

namespace Ushahidi\Addons\Rackspace;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use OpenStack\OpenStack;
use OpenStack\Identity\v2\Service;
use OpenStack\Common\Transport\Utils as TransportUtils;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class RackspaceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('rackspace', function ($app, $config) {
            $httpClient = new Client([
                'base_uri' => TransportUtils::normalizeUrl($config['authUrl']),
                'handler'  => HandlerStack::create(),
            ]);

            $options = [
                'authUrl'         => $config['authUrl'],
                'region'          => $config['region'],
                'username'        => $config['username'],
                'password'        => $config['password'],
                'tenantId'        => $config['tenantid'],
                'user'            => [
                    'id'       => $config['username'],
                    'password' => $config['password'],
                ],
                'identityService' => Service::factory($httpClient),
            ];

            $openstack = new OpenStack($options);

            $store = $openstack->objectStoreV1([
                'catalogName' => 'cloudFiles',
            ]);

            $account = $store->getAccount();
            $container = $store->getContainer($config['container']);

            return new Filesystem(
                new RackspaceAdapter($container, $account),
                $config
            );
        });
    }
}
