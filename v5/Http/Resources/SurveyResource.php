<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
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
        /**
         * @TODO
         * Replace with an includes=? and format=? system
         */
        if ($request->query('format') === 'minimal') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'translations' => new TranslationCollection($this->translations),
                'enabled_languages' => [
                    'default'=> $this->base_language,
                    'available' => $this->translations->groupBy('language')->keys()
                ]
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type'  => $this->type,
            'disabled' => $this->disabled,
            'require_approval' => (boolean) $this->require_approval,
            'everyone_can_create' => (boolean) $this->everyone_can_create,
            'color'  => $this->color,
            'hide_author' => (boolean) $this->hide_author,
            'hide_time' => (boolean) $this->hide_time,
            'hide_location' => (boolean) $this->hide_location,
            'targeted_survey' => (boolean) $this->targeted_survey,
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
