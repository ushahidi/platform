<?php

namespace Ushahidi\Addons\Rackspace;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use OpenStack\OpenStack;
use OpenStack\Common\Service\Builder;
use OpenStack\Identity\v2\Service as IdentityService;
use OpenStack\Common\Transport\Utils as TransportUtils;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Addons\Rackspace\Identity\Api;

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
            $options = [
                'authUrl'  => $config['authUrl'],
                'region'   => $config['region'],
                'username' => $config['username'],
                'apiKey'   => $config['key'],
                'tenantId' => $config['tenantid'],
            ];

            $httpClient = new Client([
                'base_uri' => TransportUtils::normalizeUrl($options['authUrl']),
                'handler' => HandlerStack::create(),
            ]);

            $identityService = new IdentityService($httpClient, new Api());

            /** @var \Ushahidi\Addons\Rackspace\CDN\Service $cdnService */
            $cdnService = $this->cdnService($options, $identityService);
            $objectStoreService = $this->objectStoreService($options, $identityService);

            $account = $objectStoreService->getAccount();
            $container = $objectStoreService->getContainer($config['container']);
            $cdnContainer = $cdnService->getContainer($config['container']);

            $adapter = new RackspaceAdapter($container, $account);
            $adapter->setCdnContainer($cdnContainer);

            return new Filesystem(
                $adapter,
                $config
            );
        });
    }

    protected function cdnService(array $options = [], $identityService)
    {
        if (!isset($options['identityService'])) {
            $options['identityService'] = $identityService;
        }

        $openstack = new Builder($options, 'Ushahidi\Addons');

        return $openstack->createService('Rackspace\CDN', [
            'catalogName' => 'cloudFilesCDN',
            'catalogType' => 'rax:object-cdn',
        ]);
    }

    protected function objectStoreService(array $options = [], $identityService)
    {
        if (!isset($options['identityService'])) {
            $options['identityService'] = $identityService;
        }

        $openstack = new OpenStack($options);

        return $openstack->objectStoreV1([
            'catalogName' => 'cloudFiles',
        ]);
    }
}
