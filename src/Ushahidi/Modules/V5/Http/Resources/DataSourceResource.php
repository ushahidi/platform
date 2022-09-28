<?php
namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DataSourceResource extends Resource
{
    /**
     * The Datasource
     *
     * @var \Ushahidi\DataSource\Contracts\DataSource
     */
    public $resource;

    public static $wrap = 'results';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'options' => $this->formatOptions($this->resource->getOptions()),
            'services' => $this->resource->getServices(),
            'inbound_fields' => $this->resource->getInboundFields(),
        ];
    }

    protected function formatOptions(array $options)
    {
        foreach ($options as $name => $input) {
            if (isset($input['description']) and $input['description'] instanceof \Closure) {
                $options[$name]['description'] = $options[$name]['description']();
            }

            if (isset($input['label']) and $input['label'] instanceof \Closure) {
                $options[$name]['label'] = $options[$name]['label']();
            }

            if (isset($input['rules']) and $input['rules'] instanceof \Closure) {
                $options[$name]['rules'] = $options[$name]['rules']();
            }
        }
        return $options;
    }
}
