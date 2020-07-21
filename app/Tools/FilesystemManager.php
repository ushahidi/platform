<?php

namespace Ushahidi\App\Tools;

class FilesystemManager extends \Illuminate\Filesystem\FilesystemManager
{
    // This is rather long, because what we cache here rarely (if ever) changes
    const CACHE_LIFETIME = 24 * 3600 ;       # in seconds

    // Gets SSL URL for rackspace object (and stores result in cache)
    protected function rackspaceUrl(string $path) : string
    {
        try {
            return $this->app['cache']->remember(
                'Ushahidi\App\Tools\FilesystemManager.rackspaceUrl[' . $path . ']',
                self::CACHE_LIFETIME,
                function () use ($path) {
                    return (string) $this->getAdapter()
                        ->getContainer()
                        ->getObject($path)
                        ->getPublicUrl(\OpenCloud\ObjectStore\Constants\UrlType::SSL);
                }
            );
        } catch (\OpenCloud\ObjectStore\Exception\ObjectNotFoundException $e) {
            return "";
        }
    }

    public function url(string $path) : string
    {
        // Special handling for Rackspace Cloudfiles to get SSL URLs
        if ($this->getAdapter() instanceof \League\Flysystem\Rackspace\RackspaceAdapter) {
            return $this->rackspaceUrl($path);
        } else {
            return url(parent::url($path));
        }
    }
}
