<?php
namespace Ushahidi\Addons\Rackspace\CDN;

use OpenStack\Common\Service\AbstractService;
use Ushahidi\Addons\Rackspace\CDN\Models\Container;

class Service extends AbstractService
{
    /**
     * Retrieves a Container object and populates its name according to the value provided. Please note that the
     * remote API is not contacted.
     *
     * @param string $name The unique name of the container
     */
    public function getContainer(string $name = null): Container
    {
        return $this->model(Container::class, ['name' => $name]);
    }
}
