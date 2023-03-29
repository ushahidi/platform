<?php
namespace Ushahidi\Modules\V5\Http\Resources;

class SurveyResource extends BaseResource
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
        $data = $this->resource->toArray();
        $data['translations'] = (new TranslationCollection($this->translations))->toArray(null);
        $data['tasks'] = new TaskCollection($this->tasks);
        return $data;
    }
}
