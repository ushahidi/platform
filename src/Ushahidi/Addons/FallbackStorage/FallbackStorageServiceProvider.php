<?php

namespace Ushahidi\Addons\FallbackStorage;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class FallbackStorageServiceProvider extends ServiceProvider
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
        Storage::extend('fallback', function ($app, $config) {
            $primaryDisk = Storage::disk($config['primary']);
            $secondaryDisk = Storage::disk($config['secondary']);

            $driver1 = $primaryDisk->getDriver();
            $driver2 = $secondaryDisk->getDriver();

            # Ensure League\Flysystem\Filesystem instances
            if (!($driver1 instanceof Filesystem)) {
                throw new \InvalidArgumentException('Primary disk must be an instance of League\Flysystem\Filesystem (found '.get_class($driver1).')');
            }
            if (!($driver2 instanceof Filesystem)) {
                throw new \InvalidArgumentException('Secondary disk must be an instance of League\Flysystem\Filesystem (found '.get_class($driver2).')');
            }
            return new FallbackStorageDriver($driver1, $driver2, $config);
        });
    }
}
