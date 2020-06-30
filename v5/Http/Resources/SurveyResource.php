<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SurveyResource extends Resource
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
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'description' => $this->description,
            'type'  => $this->type,
            'disabled' => $this->disabled,
            'require_approval' => $this->require_approval,
            'everyone_can_create' => $this->everyone_can_create,
            'color'  => $this->color,
            'hide_author' => $this->hide_author,
            'hide_time' => $this->hide_time,
            'hide_location' => $this->hide_location,
            'targeted_survey' => $this->targeted_survey,
            'translations' => new TranslationCollection($this->translations),
            'tasks' => new TaskCollection($this->tasks),
            'can_create' => $this->can_create,
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ]
        ];
    }
}
