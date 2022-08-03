<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemServiceProvider as LaravelFilesystemServiceProvider;
use App\Tools\FilesystemManager;

class FilesystemServiceProvider extends LaravelFilesystemServiceProvider
{
    protected function registerManager()
    {
        $this->app->singleton('filesystem', function () {
            return new FilesystemManager($this->app);
        });
    }
}
