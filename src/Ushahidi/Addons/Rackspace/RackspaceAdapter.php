<?php

namespace Ushahidi\Addons\Rackspace;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Adapter\Polyfill\StreamedCopyTrait;
use League\Flysystem\Config;
use League\Flysystem\Util;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\ObjectStore\v1\Models\Account;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Models\StorageObject;

use Throwable;

// Based off https://github.com/thephpleague/flysystem-rackspace/pull/26
final class RackspaceAdapter extends AbstractAdapter
{
    use StreamedCopyTrait;
    use NotSupportingVisibilityTrait;

    private $container;

    private $prefix;

    private $account;

    public function __construct(Container $container, Account $account, string $prefix = '')
    {
        $this->setPathPrefix($prefix);
        $this->account = $account;
        $this->container = $container;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    protected function getObject(string $path): StorageObject
    {
        $location = $this->applyPathPrefix($path);

        return $this->container->getObject($location);
    }

    protected function getPartialObject(string $path): StorageObject
    {
        $location = $this->applyPathPrefix($path);

        return $this->container->getObject($location);
    }

    public function write($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $headers = $config->get('headers', []);

        $response = $this->container->createObject([
            'name' => $location,
            'content' => $contents,
            'headers' => $headers,
        ]);

        return $this->normalizeObject($response);
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        $object = $this->getObject($path);
        $object->setContent($contents);
        $object->setEtag(null);
        $response = $object->update();

        if (!$response->lastModified) {
            return false;
        }

        return $this->normalizeObject($response);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        $object = $this->getObject($path);
        $newlocation = $this->applyPathPrefix($newpath);
        $destination = sprintf('/%s/%s', $this->container->name, ltrim($newlocation, '/'));
        try {
            $object->copy(['destination' => $destination]);
        } catch (Throwable $exception) {
            return false;
        }

        $object->delete();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        try {
            $location = $this->applyPathPrefix($path);

            $this->container->getObject($location)->delete();
        } catch (Throwable $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        $location = $this->applyPathPrefix($dirname);
        $objects = $this->container->listObjects(['prefix' => $location]);

        try {
            foreach ($objects as $object) {
                /* @var $object StorageObject */
                $object->delete();
            }
        } catch (Throwable $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        $headers = $config->get('headers', []);
        $headers['Content-Type'] = 'application/directory';
        $extendedConfig = (new Config())->setFallback($config);
        $extendedConfig->set('headers', $headers);

        return $this->write($dirname, '', $extendedConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $headers = $config->get('headers', []);

        $response = $this->container->createObject([
            'name' => $location,
            'stream' => Utils::streamFor($resource),
            'headers' => $headers,
        ]);

        return $this->normalizeObject($response);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->update($path, $resource, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        try {
            $location = $this->applyPathPrefix($path);
            $exists = $this->container->objectExists($location);
        } catch (BadResponseError | Exception $exception) {
            return false;
        }

        return $exists;
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        $object = $this->getObject($path);
        $data = $this->normalizeObject($object);

        $stream = $object->download();
        $data['contents'] = $stream->read($object->contentLength);
        $stream->close();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        $object = $this->getObject($path);
        $responseBody = $object->download();
        $responseBody->rewind();

        return ['stream' => $responseBody->detach()];
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        $response = [];
        $marker = null;
        $location = $this->applyPathPrefix($directory);

        while (true) {
            $objectList = $this->container->listObjects(['prefix' => $location, 'marker' => $marker]);
            if (null === $objectList->current()) {
                break;
            }

            $response = array_merge($response, iterator_to_array($objectList));
            $marker = end($response)->name;
        }

        return Util::emulateDirectories(array_map([$this, 'normalizeObject'], $response));
    }

    /**
     * {@inheritdoc}
     */
    protected function normalizeObject(StorageObject $object)
    {
        $name = $object->name;
        $name = $this->removePathPrefix($name);
        $mimetype = explode('; ', $object->contentType);

        return [
            'type' => ((in_array('application/directory', $mimetype)) ? 'dir' : 'file'),
            'dirname' => Util::dirname($name),
            'path' => $name,
            'timestamp' => $object->lastModified,
            'mimetype' => reset($mimetype),
            'size' => $object->contentLength,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        $object = $this->getPartialObject($path);

        return $this->normalizeObject($object);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @param string $path
     */
    public function applyPathPrefix($path): string
    {
        $encodedPath = join('/', array_map('rawurlencode', explode('/', $path)));

        return parent::applyPathPrefix($encodedPath);
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string $path
     * @return string
     */
    public function getUrl($path)
    {
        return (string) $this->container->getObject($path)->getPublicUri();
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @param  array  $options
     * @return string
     */
    public function getTemporaryUrl($path, $expiration, array $options = [])
    {
        $object = $this->container->getObject($path);

        $url = $object->getPublicUri();
        $expires = Carbon::now()->diffInSeconds($expiration);
        $method = strtoupper($options['method'] ?? 'GET');
        $expiry = time() + (int) $expires;

        // check for proper method
        if ($method != 'GET' && $method != 'PUT') {
            throw new Exception(sprintf(
                'Bad method [%s] for TempUrl; only GET or PUT supported',
                $method
            ));
        }

        if (!($secret = $this->account->getMetadata()['Temp-Url-Key'])) {
            throw new Exception('Cannot produce temporary URL without an account secret.');
        }

        // if ($forcePublicUrl === true) {
        //     $url->setHost($this->getService()->getEndpoint()->getPublicUrl()->getHost());
        // }

        $urlPath = urldecode($url->getPath());
        $body = sprintf("%s\n%d\n%s", $method, $expiry, $urlPath);
        $hash = hash_hmac('sha1', $body, $secret);

        return sprintf('%s?temp_url_sig=%s&temp_url_expires=%d', $url, $hash, $expiry);
    }
}
