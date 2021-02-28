<?php


namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use v5\Models\Category;

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
        $tasks = $this->collection
                    ->get('tasks')
                    ->unique()
                    ->sortBy('priority')
                    ->values();

        $fields_by_task = $this->collection->get('values')->mapToGroups(function ($item) {
            return [$item->attribute->form_stage_id => $item];
        });

        $tasks = $tasks->map(function ($task, $key) use ($fields_by_task) {
            $fields = $task->fields->sortBy('priority')->values();
            $values = $fields_by_task->get($task->id);
            $task_trans = new TranslationCollection($task->translations);
            $task = $task->toArray();
            $task['translations'] = $task_trans;
            $task['fields'] = $fields->map(function ($field, $key) use ($values) {
                $field->load('translations');
                $field_obj = $field;
                $trans = new TranslationCollection($field->translations);
                $field = $field->toArray();
                $field['translations'] = $trans;
                $field['value'] = null;
                if (!$values) {
                    return $field;
                }
                $field['value'] = $values->filter(function ($value, $key) use ($field) {
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
                    } elseif ($field['type'] === 'tags') {
                        $field['value'] = $this->makeCategoryValue($field['value']);
                    } else {
                        $field['value'] = $this->makeValue($field['value']);
                    }
                }
                return $field;
            });
            return $task;
        });

        return $tasks->values();
    }

    private function makeValue($value)
    {
        $value_trans = new TranslationCollection($value['translations']);
        $value = $value->makeHidden('attribute')->makeHidden('post')->toArray();
        $value['translations'] = $value_trans;
        return $value;
    }
    private function makeCategoryValue($value)
    {
        return $value->map(function ($f) {
            if ($f->tag) {
                return CategoryResource::make($f->tag);
            }
            $c = new Category();
            $c->setAttribute('id', $f->tag_id);
            return ForbiddenCategoryResource::make($c);
        });
    }
    private function makeCollectionItem()
    {
    }
    private function makeTask()
    {
    }
}
