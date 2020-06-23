<?php
namespace v4\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

class PostResource extends Resource
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
            'user_id' => $this->user_id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'author_email' => $this->author_email,
            'author_realname' => $this->author_realname,
            'status' => $this->status,
            'published_to' => $this->published_to,
            'locale' => $this->locale,
            'created' => $this->created,
            'updated' => $this->updated,
            'post_date' => $this->post_date,
            'base_language' => $this->base_language,
            'categories' => $this->categories,
            'completed_stages' => $this->postStages,
            'survey' => $this->survey,
            'post_content' => $this->postValues($this->values()),
            'translations' => new TranslationCollection($this->translations),
            'enabled_languages' => [
                'default'=> $this->base_language,
                'available' => $this->translations->groupBy('language')->keys()
            ]
        ];
    }

    public function postValues($values)
    {
        if ($values->count() === 0) {
            return $this->survey->tasks;
        }
        $tasks = new Collection();
        $values->each(function ($item, $key) use ($tasks) {
            if ($item->attribute) {
                $tasks->push($item->attribute->stage);
            }
        });
        $tasks = $tasks->unique()->sortBy('priority')->values();

        $grouped = $values->mapToGroups(function ($item) {
            return [$item->attribute->form_stage_id => $item];
        });

        $tasks = $tasks->map(function ($task, $key) use ($grouped) {
            $fields = $task->fields->sortBy('priority')->values();
            $values_by_task = $grouped->get($task->id);
            $task = $task->toArray();

            $task['fields'] = $fields->map(function ($field, $key) use ($values_by_task) {
                $field = $field->toArray();
                $field['value'] = $values_by_task->filter(function ($value, $key) use ($field) {
                    return $value->form_attribute_id == $field['id'];
                })->values();
                if ($field['type'] !== 'tags') {
                    $field['value'] = $field['value']->first();
                }
                return $field;
            });
            return $task;
        });

        return $tasks->values();
    }
}
