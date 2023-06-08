<?php

namespace Ushahidi\Modules\V5\Http\Resources\Post;

use Illuminate\Support\Collection;
use Ushahidi\Core\Ohanzee\Entities\Post;
use Ushahidi\Modules\V5\Models\Post\Post as v5Post;
use Ushahidi\Modules\V5\Http\Resources\BaseResource;
use Ushahidi\Modules\V5\Http\Resources\LockCollection;
use Ushahidi\Modules\V5\Http\Resources\PostValueCollection;
use Ushahidi\Modules\V5\Http\Resources\Survey\TaskCollection;
use Ushahidi\Modules\V5\Http\Resources\TranslationCollection;
use Ushahidi\Modules\V5\Http\Resources\ContactPointerResource;
use Ushahidi\Modules\V5\Http\Resources\MessagePointerResource;

class PostResource extends BaseResource
{
    public static $wrap = 'result';
    private const DEFAULT_SOURCE_TYPE = 'web';
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
        $authorizer = service('authorizer.post');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $resource_array = $this->resource->attributesToArray();
        unset($resource_array['completed_stages']);
        $entity = new Post($resource_array);
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        return $authorizer->getAllowedPrivs($entity);
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // @TODO-jan27 make translations and enabled_languages optional
        // @TODO-jan27 make id required
        $data = $this->resource->toArray();
        if (isset($data['translations'])) {
            $data['translations'] = (new TranslationCollection($this->translations))->toArray(null);
        }
        if (isset($data['sets'])) {
            $set_ids = [];
            foreach ($data['sets'] as $set) {
                $set_ids[] = $set['id'];
            }
            $data['sets'] = $set_ids;
        }

        $data ['allowed_privileges'] = $this->getResourcePrivileges();
        return $data;
    }
}
