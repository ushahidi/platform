<?php

namespace Ushahidi\Addons\Rackspace;

use OpenCloud\Rackspace;
use League\Flysystem\Filesystem;
use League\Flysystem\Rackspace\RackspaceAdapter;
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
            $client = new Rackspace($config['endpoint'], [
                'username' => $config['username'], 'apiKey' => $config['key'],
            ]);

            $store = $client -> objectStoreService('cloudFiles', $config['region'], $config['urlType'] ?? null);

            return new Filesystem(
                new RackspaceAdapter($store -> getContainer($config['container']), $config['root'] ?? null), $config
            );
        });
    }
}
