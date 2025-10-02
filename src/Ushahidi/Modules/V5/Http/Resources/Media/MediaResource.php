<?php
namespace Ushahidi\Modules\V5\Http\Resources\Media;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Core\Entity\Media as MediaEntity;
use Illuminate\Support\Facades\Storage;


use App\Bus\Query\QueryBus;

class MediaResource extends Resource
{

    // use RequestCachedResource;

    public static $wrap = 'result';
    private function getResourcePrivileges()
    {
        $authorizer = service('authorizer.media');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $entity = new MediaEntity($this->resource->toArray());
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
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'caption' => $this->caption,
            'mime'=> $this->mime,
            'original_file_url'=> $this->formatOFilename($this->o_filename),
            'original_file_size'=> $this->o_size,
            'original_width'=> $this->o_width,
            'original_height'=> $this->o_height,
            'created'=> $this->created,
            'updated'=> $this->updated,
            'allowed_privileges' => $this->getResourcePrivileges()



        ];
        $data = $this->resource->toArray();
        $data['allowed_privileges'] = $this->getResourcePrivileges();
        return $data;
    }

    protected function formatOFilename($value)
    {
        // Removes path from image file name, encodes the filename, and joins the path and filename together
        $url_path = explode("/", $value);
        $filename = array_pop($url_path);
        array_push($url_path, $filename);
        $path = implode("/", $url_path);
        return Storage::url($path);
    }
}
