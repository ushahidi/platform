<?php


namespace v5\Http\Resources;

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
    public $collects = 'v5\Http\Resources\PostValueResource';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Collection
     */
    public function toArray($request)
    {
        $tasks = $this->collection->get('tasks');

        $tasks = $tasks->unique()->sortBy('priority')->values();

        $grouped = $this->collection->get('values')->mapToGroups(function ($item) {
            return [$item->attribute->form_stage_id => $item];
        });

        $tasks = $tasks->map(function ($task, $key) use ($grouped) {
            $fields = $task->fields->sortBy('priority')->values();
            $values_by_task = $grouped->get($task->id);
            $task_trans = new TranslationCollection($task->translations);
            $task = $task->toArray();
            $task['translations'] = $task_trans;
            $task['fields'] = $fields->map(function ($field, $key) use ($values_by_task) {
                $field->load('translations');
                $field_obj = $field;
                $trans = new TranslationCollection($field->translations);
                $field = $field->toArray();
                $field['translations'] = $trans;
                $field['value'] = null;
                if (!$values_by_task) {
                    return $field;
                }

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
                    if (get_class($field['value']) === 'v5\Http\Resources\PostValueResource') {
                        $field['value']->load('translations');
                        $field['value'] = $field['value']->toArray($field['value']);
                    } elseif (get_class($field['value']) === 'Illuminate\Support\Collection') {
                        if ($field['type'] === 'tags') {
                            $cats = $field['value']->map(function ($f) {
                                return CategoryResource::make($f->tag);
                            });
                            $field['value'] = $cats;
                        }
                    } else {
                        $field['value'] = $field['value']->toArray($field['value']);
                    }
                }
                return $field;
            });
            return $task;
        });

        return $tasks->values();
    }
}
