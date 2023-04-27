<?php
namespace Ushahidi\Modules\V5\Http\Resources\Collection;

use Illuminate\Http\Resources\Json\JsonResource;
use Ushahidi\Core\Entity\Set as CollectionEntity;

class CollectionPostResource extends JsonResource
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
    private function getResourcePrivileges()
    {
        $authorizer = service('authorizer.set');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $entity = new CollectionEntity($this->resource->attributesToArray());
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        return $authorizer->getAllowedPrivs($entity);
    }
}
