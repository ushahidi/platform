<?php

namespace Ushahidi\Modules\V5\Http\Resources\Post;

use Ushahidi\Modules\V5\Http\Resources\BaseResource;

class PostGeometryResource extends BaseResource
{
    public static $wrap = 'result';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $map_config = service('map.config');

        if (is_a($this->resource, "stdClass")) {
            $data = (array) $this->resource;
        } else {
            $data = $this->resource->toArray();
        }

        $data['geojson'] = json_decode($data['geojson'], true);

        if (isset($data['hide_location']) && $data['hide_location']) {
            foreach ($data['geojson']['features'] as $key => $feature) {
                $feature['geometry']['coordinates'][0] =
                    round($feature['geometry']['coordinates'][0], $map_config['location_precision']);
                $feature['geometry']['coordinates'][1] =
                    round($feature['geometry']['coordinates'][1], $map_config['location_precision']);
                $data['geojson']['features'][$key] = $feature;
            }
        }
        // TODO : next code just keep the first location , later we may add a flag for primary location field
        if (isset($data['geojson'])) {
            foreach ($data['geojson']['features'] as $key => $feature) {
                if ($key > 0) {
                    unset($data['geojson']['features'][$key]);
                }
            }
        }
        unset($data['hide_location']);
        return $data;
    }
}
