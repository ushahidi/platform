<?php


namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\Category;

class PostValueCollection extends ResourceCollection
{
    public static $wrap = 'results';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'Ushahidi\Modules\V5\Http\Resources\PostValueResource';

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
            $task['required'] = (bool)$task['required'];
            $task['fields'] = $fields->map(function ($field, $key) use ($values) {
                $field->load('translations');
                $field_obj = $field;
                $trans = new TranslationCollection($field->translations);
                $field = $field->toArray();
                $field['translations'] = $trans;
                $field['required'] = (bool)$field['required'];
                $field['response_private'] = (bool)$field['response_private'];
                $field['value'] = null;
                if (!$values) {
                    return $field;
                }
                $field['value'] = $values->filter(function ($value, $key) use ($field) {
                    return $value->form_attribute_id == $field['id'];
                })->values();

                if ($field['type'] === 'tags') {
                    $field['options'] = $field['options'] ?
                                        new CategoryCollection($field_obj->options) :
                                        $field['options'];
                } elseif ($field['type'] === 'media') {
                    $field['value'] = $this->makeMediaValue($field['value']);
                } else {
                    $field['value'] = $field['value']->first();
                }

                if (!empty($field['value'])) {
                    if (get_class($field['value']) === 'Ushahidi\Modules\V5\Http\Resources\PostValueResource') {
                        $field['value']->load('translations');
                        $field['value'] = $field['value']->toArray($field['value']);
                    } elseif ($field['type'] === 'tags') {
                        $field['value'] = $this->makeCategoryValue($field['value']);
                    } elseif ($field['type'] === 'media') {
                        $field['value'] = $field['value']->toArray($field['value']);
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
        if (isset($value['metadata'])) {
            $value['value_meta'] = $value['metadata'];
            if (isset($value['value_meta']['is_date']) && $value['value_meta']['is_date'] == true) {
                $value['value'] = date("Y-m-d", strtotime($value['value']));
            }
            unset($value['metadata']);
        }
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

    private function makeMediaValue($values)
    {
        return $values->map(function ($item, $key) {
            return $item->getOriginal();
        });
    }

    private function convertBooleanTaskValues($Task)
    {
    }

    private function makeCollectionItem()
    {
    }

    private function makeTask()
    {
    }
}
