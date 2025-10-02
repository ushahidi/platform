<?php

namespace Ushahidi\Addons\FallbackStorage;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Adapter\Polyfill\StreamedCopyTrait;

final class FallbackStorageAdapter extends AbstractAdapter
{
    use StreamedCopyTrait;
    use NotSupportingVisibilityTrait;

    private AbstractAdapter $primaryAdapter;
    private AbstractAdapter $secondaryAdapter;

    public function __construct(
        AbstractAdapter $primaryAdapter,
        AbstractAdapter $secondaryAdapter
    ) {
        $this->primaryAdapter = $primaryAdapter;
        $this->secondaryAdapter = $secondaryAdapter;
    }

    public function write($path, $contents, $config = [])
    {
        // Only write to primaryAdapter, no fallback needed
        return $this->primaryAdapter->write($path, $contents, $config);
    }

    public function writeStream($path, $resource, $config = [])
    {
        // Only write to primaryAdapter, no fallback needed
        return $this->primaryAdapter->writeStream($path, $resource, $config);
    }

    public function update($path, $contents, $config = [])
    {
        // Try primaryAdapter, no fallback allowed
        return $this->primaryAdapter->update($path, $contents, $config);
    }

    public function updateStream($path, $resource, $config = [])
    {
        // Try primaryAdapter, no fallback allowed
        return $this->primaryAdapter->updateStream($path, $resource, $config);
    }

    public function rename($path, $newpath): bool
    {
        // Try primaryAdapter, no fallback allowed
        if ($this->primaryAdapter->has($path)) {
            if ($this->primaryAdapter->rename($path, $newpath)) {
                return true;
            }
        }
        return false;
    }

    public function copy($path, $newpath)
    {
        // Try primaryAdapter if the path is there,
        // Otherwise check secondaryAdapter and stream copy from there to primaryAdapter
        if ($this->primaryAdapter->has($path)) {
            return $this->primaryAdapter->copy($path, $newpath);
        }
        if ($this->secondaryAdapter->has($path)) {
            $stream = $this->secondaryAdapter->readStream($path);
            if ($stream === false || !isset($stream['stream'])) {
                return false;
            }
            /* TODO: secondaryAdapter to primaryAdapter stream copy */
            // $result = $this->primaryAdapter->writeStream($newpath, $stream['stream']);
            // if (is_resource($stream['stream'])) {
            //     fclose($stream['stream']);
            // }
            // return $result;
            return false;
        }
        // Not found in either, return false
        return false;
    }

    public function delete($path)
    {
        // Only delete from primaryAdapter, no deletion on secondaryAdapter
        return $this->primaryAdapter->delete($path);
    }

    public function deleteDir($dirname)
    {
        // Only delete from primaryAdapter, no deletion on secondaryAdapter
        return $this->primaryAdapter->deleteDir($dirname);
    }

    public function createDir($dirname, $config = [])
    {
        // Only create in primaryAdapter, no creation in secondaryAdapter
        return $this->primaryAdapter->createDir($dirname, $config);
    }

    public function has($path)
    {
        // If primaryAdapter knows about it, return true; otherwise check secondaryAdapter
        if ($this->primaryAdapter->has($path)) {
            return true;
        }
        return $this->secondaryAdapter->has($path);
    }

    public function read($path)
    {
        $result = $this->primaryAdapter->read($path);
        if ($result === false) {
            $result = $this->secondaryAdapter->read($path);
        }
        return $result;
    }

    public function readStream($path)
    {
        $result = $this->primaryAdapter->readStream($path);
        if ($result === false) {
            $result = $this->secondaryAdapter->readStream($path);
        }
        return $result;
    }

    public function listContents($directory = '', $recursive = false)
    {
        $primaryAdapterList = $this->primaryAdapter->listContents($directory, $recursive);
        $secondaryAdapterList = $this->secondaryAdapter->listContents($directory, $recursive);

        $combined = [];
        $seen = [];

        foreach ((array) $primaryAdapterList as $entry) {
            if (isset($entry['path'])) {
                $seen[$entry['path']] = true;
            }
            $combined[] = $entry;
        }

        foreach ((array) $secondaryAdapterList as $entry) {
            if (isset($entry['path']) && isset($seen[$entry['path']])) {
                continue;
            }
            if (isset($entry['path'])) {
                $seen[$entry['path']] = true;
            }
            $combined[] = $entry;
        }

        return $combined;
    }

    public function getMetadata($path)
    {
        $result = $this->primaryAdapter->getMetadata($path);
        if ($result === false) {
            $result = $this->secondaryAdapter->getMetadata($path);
        }
        return $result;
    }

    public function getSize($path)
    {
        $result = $this->primaryAdapter->getSize($path);
        if ($result === false) {
            $result = $this->secondaryAdapter->getSize($path);
        }
        return $result;
    }

    public function getMimetype($path)
    {
        $result = $this->primaryAdapter->getMimetype($path);
        if ($result === false) {
            $result = $this->secondaryAdapter->getMimetype($path);
        }
        return $result;
    }

    public function getTimestamp($path)
    {
        $result = $this->primaryAdapter->getTimestamp($path);
        if ($result === false) {
            $result = $this->secondaryAdapter->getTimestamp($path);
        }
        return $result;
    }

}
