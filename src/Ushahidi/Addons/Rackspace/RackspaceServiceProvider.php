<?php

namespace Ushahidi\Addons\Rackspace;

use GuzzleHttp\Client;
use OpenStack\OpenStack;
use GuzzleHttp\HandlerStack;
use League\Flysystem\Filesystem;
use OpenStack\Identity\v2\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Addons\Rackspace\Identity\Api;
use OpenStack\Common\Transport\Utils as TransportUtils;

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
            $client = new Client([
                'base_uri' => TransportUtils::normalizeUrl($config['authUrl']),
                'handler'  => HandlerStack::create(),
            ]);

            $options = [
                'authUrl'         => $config['authUrl'],
                'region'          => $config['region'],
                'username'        => $config['username'],
                'apiKey'          => $config['key'],
                'tenantId'        => $config['tenantid'],
                'identityService' => new Service($client, new Api()),
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
