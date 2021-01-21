<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'form_id' => $this->form_id,
            'label' => $this->label,
            'priority' => $this->priority,
            'required' => (boolean) $this->required,
            'type' => $this->type,
            'description' => $this->description,
            'show_when_published' => $this->show_when_published,
            'task_is_internal_only' => $this->task_is_internal_only,
            'fields' => new FieldCollection($this->fields),
            'translations' => new TranslationCollection($this->translations)
        ];
    }
}
