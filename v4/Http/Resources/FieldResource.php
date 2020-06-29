<?php
namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FieldResource extends Resource
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
        return [
            'id' => $this->id,
            'key' => $this->key,
            'label' => $this->label,
            'instructions' => $this->instructions,
            'input' => $this->input,
            'type' => $this->type,
            'required' => $this->required,
            'default' => $this->default,
            'priority' => $this->priority,
            'options' => ($this->type === 'tags') ? new CategoryCollection($this->options) : $this->options,
            'cardinality' => $this->cardinality,
            'config' => $this->config,
            'response_private' => $this->response_private,
            'form_stage_id' => $this->form_stage_id,
            'translations' => new TranslationCollection($this->translations),
        ];
    }
}
