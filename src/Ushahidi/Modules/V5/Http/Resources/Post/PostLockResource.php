<?php

namespace Ushahidi\Modules\V5\Http\Resources\Post;

use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Modules\V5\Http\Resources\BaseResource;

class PostLockResource extends BaseResource
{
    public static $wrap = 'result';
    private $http_status;
    public function __construct($resource, $status = 200)
    {
        parent::__construct($resource);
        $this->http_status = $status;
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode((int)$this->http_status);
    }



    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource->toArray();
        return $data;
    }
}
