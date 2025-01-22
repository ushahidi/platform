<?php
namespace Ushahidi\Modules\V5\Http\Resources\Survey;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Modules\V5\Http\Resources\TranslationCollection;

class TaskResource extends Resource
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
            'translations' => (new TranslationCollection($this->translations))->toArray(null)
        ];
    }
}
