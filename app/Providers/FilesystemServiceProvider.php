<?php

namespace Ushahidi\App\Providers;

use Illuminate\Filesystem\FilesystemServiceProvider as LaravelFilesystemServiceProvider;
use Ushahidi\App\Tools\FilesystemManager;

class FilesystemServiceProvider extends LaravelFilesystemServiceProvider
{
    protected function registerManager()
    {
        $this->app->singleton('filesystem', function () {
            return new FilesystemManager($this->app);
        });
    }
}
