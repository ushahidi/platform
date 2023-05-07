<?php
namespace Ushahidi\Addons\Rackspace\CDN\Models;

use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\ObjectStore\v1\Models\MetadataTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * @property \Ushahidi\Addons\Rackspace\CDN\Api $api
 */
class Container extends OperatorResource implements Retrievable
{
    use MetadataTrait;

    /** @var string */
    public $name;

    /** @var array */
    public $metadata;

    const METADATA_PREFIX = 'X-Cdn-';

    /**
     * {@inheritdoc}
     */
    public function populateFromResponse(ResponseInterface $response): self
    {
        parent::populateFromResponse($response);

        $this->metadata = $this->parseMetadata($response);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->headContainer());
        $this->populateFromResponse($response);
    }

    /**
     * @return null|string|int
     */
    public function getCdnSslUri()
    {
        return $this->metadata['Ssl-Uri'];
    }
}
