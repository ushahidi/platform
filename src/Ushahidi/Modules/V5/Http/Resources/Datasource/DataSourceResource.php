<?php

namespace Ushahidi\Modules\V5\Http\Resources\Datasource;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class DataSourceResource extends Resource
{
    public static $wrap = 'results';

    public function toArray($request): array
    {
        $options = $this->resource->getOptions();
        $this->formatOptions($options);

        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'options' => $options,
            'services' => $this->resource->getServices(),
            'inbound_fields' => $this->resource->getInboundFields(),
        ];
    }

    private function formatOptions(array &$options): void
    {
        foreach ($options as $name => $input) {
            if ($this->isElementCallable('description', $input)) {
                $options[$name]['description'] = $input['description']();
            }

            if ($this->isElementCallable('label', $input)) {
                $options[$name]['label'] = $input['label']();
            }

            if ($this->isElementCallable('rules', $input)) {
                $options[$name]['rules'] = $input['rules']();
            }
        }
    }

    private function isElementCallable(string $key, array $array): bool
    {
        return array_key_exists($key, $array) && $array[$key] instanceof \Closure;
    }
}
