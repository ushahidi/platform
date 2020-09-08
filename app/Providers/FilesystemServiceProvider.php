<?php

namespace Ushahidi\App\Providers;

class FilesystemServiceProvider extends \Illuminate\Filesystem\FilesystemServiceProvider
{
    protected function registerManager()
    {
        $this->app->singleton('filesystem', function () {
            return new \Ushahidi\App\Tools\FilesystemManager($this->app);
        });
    }
}
