<?php

namespace Ushahidi\App\Tools;

use Illuminate\Filesystem\FilesystemManager as LaravelFilesystemManager;
use League\Flysystem\Rackspace\RackspaceAdapter;
use OpenCloud\ObjectStore\Exception\ObjectNotFoundException;

class FilesystemManager extends LaravelFilesystemManager
{
    // This is rather long, because what we cache here rarely (if ever) changes
    const CACHE_LIFETIME = 24 * 3600;       // in seconds

    // Gets SSL URL for rackspace object (and stores result in cache)
    protected function rackspaceUrl(string $path) : string
    {
        try {
            return $this->app['cache']->remember(
                self::class."rackspaceUrl[$path]",
                self::CACHE_LIFETIME,
                function () use ($path) {
                    return (string) $this->getAdapter()
                        ->getContainer()
                        ->getObject($path)
                        ->getPublicUrl(\OpenCloud\ObjectStore\Constants\UrlType::SSL);
                }
            );
        } catch (ObjectNotFoundException $e) {
            return '';
        }
    }

    public function url(string $path) : string
    {
        // Special handling for Rackspace Cloudfiles to get SSL URLs
        if ($this->getAdapter() instanceof RackspaceAdapter) {
            return $this->rackspaceUrl($path);
        } else {
            return url(parent::url($path));
        }
    }
}
