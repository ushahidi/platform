<?php

namespace Ushahidi\Addons\FallbackStorage;

use CaptainHook\App\Runner\Files;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Filesystem;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;

class FallbackStorageDriver extends Filesystem
{
    
    private Filesystem $primary;
    private Filesystem $secondary;

    public function __construct(Filesystem $primary, Filesystem $secondary, array $config = [])
    {
        $this->primary = $primary;
        $this->secondary = $secondary;
        $adapter = new FallbackStorageAdapter($primary->getAdapter(), $secondary->getAdapter());

        parent::__construct($adapter, $config);
    }

    public function getUrl($path)
    {
        // Try primary first
        $url = $this->getUrlWrapper($this->primary, $path);
        Log::debug('FallbackStorageDriver: getUrl('.$path.') primary url='.($url ?: 'null'));
        if ($url) {
            return $url;
        }

        // Fallback to secondary
        $url = $this->getUrlWrapper($this->secondary, $path);
        Log::debug('FallbackStorageDriver: getUrl('.$path.') secondary url='.($url ?: 'null'));
        return $url;
    }

    private function getUrlWrapper($driver, $path)
    {
        if (!$driver->has($path)) {
            return null;
        }
        if (method_exists($driver, 'getUrl')) {
            return $driver->getUrl($path);
        }
        $fsAdapter = new FilesystemAdapter($driver);
        return $fsAdapter->url($path);
    }

}
