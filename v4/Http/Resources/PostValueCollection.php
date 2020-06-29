<?php


namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class PostValueCollection extends ResourceCollection
{
    public static $wrap = 'results';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'v4\Http\Resources\PostValueResource';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Collection
     */
    public function toArray($request)
    {
        $tasks = new Collection();

        $this->collection->each(function ($item, $key) use ($tasks) {
            if ($item->attribute) {
                $tasks->push($item->attribute->stage);
            }
        });

        $tasks = $tasks->unique()->sortBy('priority')->values();

        $grouped = $this->collection->mapToGroups(function ($item) {
            return [$item->attribute->form_stage_id => $item];
        });

        $tasks = $tasks->map(function ($task, $key) use ($grouped) {
            $fields = $task->fields->sortBy('priority')->values();
            $values_by_task = $grouped->get($task->id);
            $task = $task->toArray();

            $task['fields'] = $fields->map(function ($field, $key) use ($values_by_task) {
                $field->load('translations');
                $field_obj = $field;
                $trans = new TranslationCollection($field->translations);
                $field = $field->toArray();
                $field['translations'] = $trans;
                $field['value'] = $values_by_task->filter(function ($value, $key) use ($field) {
                    return $value->form_attribute_id == $field['id'];
                })->values();
                if ($field['type'] !== 'tags') {
                    $field['value'] = $field['value']->first();
                } else {
                    $field['options'] =
                        $field['options'] ? new CategoryCollection($field_obj->options) :
                        $field['options'];
                }
                if (!empty($field['value'])) {
                    if (get_class($field['value']) === 'v4\Http\Resources\PostValueResource') {
                        $field['value']->load('translations');
                    }
                    $field['value'] = $field['value']->toArray($field['value']);
                }
                return $field;
            });
            return $task;
        });

        return $tasks->values();
    }
}
