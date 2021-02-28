<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use v5\Http\Controllers\SurveyController;
use v5\Models\Survey;

class SurveyResource extends BaseResource
{
    public static $wrap = 'result';
    /*
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function includeResourceFields($request)
    {
        return self::includeFields($request, [
            'id',
            'name',
            'description',
            'type',
            'disabled',
            'require_approval',
            'everyone_can_create',
            'color',
            'hide_author',
            'hide_time',
            'hide_location',
            'targeted_survey',
            'can_create'
        ]);
    }
    private function hydrateResourceRelationships($request)
    {
        $hydrate = $this->getHydrate(Survey::$relationships, $request);
        $result = [];
        foreach ($hydrate as $relation) {
            switch ($relation) {
                case 'tasks':
                    $result['tasks'] = new TaskCollection($this->tasks);
                    break;
                case 'translations':
                    $result['translations'] = new TranslationCollection($this->translations);
                    break;
                case 'enabled_languages':
                    $result['enabled_languages'] = [
                        'default'=> $this->base_language,
                        'available' => $this->translations->groupBy('language')->keys()
                    ];
                    break;
            }
        }
        return $result;
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
        $fields = $this->includeResourceFields($request);
        $result = $this->setResourceFields($fields);
        $hydrated = $this->hydrateResourceRelationships($request);
        return array_merge($result, $hydrated);
    }
}
