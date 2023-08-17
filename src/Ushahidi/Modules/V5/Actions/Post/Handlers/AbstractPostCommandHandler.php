<?php
    namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

    use Ushahidi\Modules\V5\Actions\V5CommandHandler;
    use Ushahidi\Modules\V5\Models\Post\Post;
    use Illuminate\Support\Facades\DB;
    
abstract class AbstractPostCommandHandler extends V5CommandHandler
{

    protected function savePostStages(Post $post, array $completed)
    {
        $post->postStages()->delete();
        foreach ($completed as $stage_id) {
            $post->postStages()->create(['post_id' => $post, 'form_stage_id' => $stage_id, 'completed' => 1]);
        }
    }


    /**
 * @param Post $post
 * @param array $post_content
 * @param int $post_id
 * @throws \Exception
 * Stage: fields
 * Fields: value, type, id
 */
    protected function savePostValues(Post $post, array $post_content, int $post_id)
    {
        $errors = [];
        $post->valuesPostTag()->delete();
        foreach ($post_content as $stage) {
            if (!isset($stage['fields'])) {
                continue;
            }
            foreach ($stage['fields'] as $field) {
                $for_delete = false;
                $type = $field['type'];
                if (!isset($field['value'])) {
                    if ($type === 'tags') {
                        continue;
                    }
                    $for_delete = true;
                }

                // We only want to check if a field input value is set, not if it's empty
                // The reason is when a field value input is updated and then left empty (as long it's not required)
                // the user wants to override the existing input value with an empty value.
                if (!isset($field['value']['value'])) {
                    if ($type === 'tags') {
                        continue;
                    }
                    $for_delete = true;
                }

               

                if ($type === 'tags') {
                    // To Do : delete the tags
                    $type === 'tags' ? 'tag' : $type;
                    $this->savePostTags($post, $field['id'], $field['value']['value']);
                    continue;
                }
             
            

                $class_name = "Ushahidi\Modules\V5\Models\PostValues\Post" . ucfirst($type);
                if (!class_exists($class_name) &&
                    in_array(
                        $class_name,
                        [
                            'Ushahidi\Modules\V5\Models\PostValues\PostTitle',
                            'Ushahidi\Modules\V5\Models\PostValues\PostDescription'
                        ]
                    )
                ) {
                    continue;
                } elseif (!class_exists($class_name)) {
                    throw new \Exception("Type '$type' is invalid.");
                }

                $post_value = $class_name::select('post_' . $type . '.*')
                    ->where('post_' . $type . '.form_attribute_id', $field['id'])
                    ->where('post_' . $type . '.post_id', $post_id)
                    ->get()
                    ->first();

                $update_id = $post_value->id ?? null; // use null coalescing operator
                if (!$update_id) {
                    $post_value = new $class_name;
                }
                // delete
                if ($for_delete) {
                    if ($update_id) {
                        $post_value->delete();
                    }
                    continue;
                }


                $value = $field['value']['value']; // field value input
                $value_meta = $field['value']['value_meta'] ?? [];
                $value_translations = $field['value']['translations'] ?? [];

                if ($type === 'geometry') {
                    if (is_string($value) && $value === '') {
                        $value = null;
                    } else {
                        $value = DB::raw("ST_GeomFromText('$value')");
                    }
                }

                $data = [
                    'post_id' => $post_id,
                    'form_attribute_id' => $field['id'],
                    'value' => $value
                ];

                if ($type === 'datetime') {
                    // We intend to save the request value as a datetime, we intend to know
                    // what format the request value is either a date or timestamp
                    if (strlen($value) == 10) {
                        $data['metadata']['is_date'] = true; // it's a date
                    } else {
                        $data['metadata']['is_date'] = false; // it's a timestamp
                    }

                    $data['metadata'] = array_merge($data['metadata'], $value_meta);
                }

                foreach ($data as $k => $v) {
                    $post_value->setAttribute($k, $v);
                }

                if ($type === 'point') {
                    $data['value'] = DB::raw("ST_GeomFromText('POINT({$value['lon']} {$value['lat']})')");
                }

                $validation = $post_value->validate();

                if ($validation) {
                    if ($update_id) { // If post value is an update
                        $post_value->update($data);
                        $this->updateTranslations(
                            new $class_name(),
                            $post_value->toArray(),
                            $value_translations,
                            $update_id,
                            "post_value_$type"
                        );
                    } else {
                        $field_value = get_class($post_value)::create($data);
                        $this->saveTranslations(
                            new $class_name(),
                            $field_value->toArray(),
                            $value_translations,
                            $field_value->id,
                            "post_value_$type"
                        );
                    }
                } else {
                    $errors['task_id.' . $stage['id'] . '.field_id.' . $field['id']]
                        = ($post_value->errors->toArray())['value'];
                }
            }
        }
        return $errors;
    }


    protected function savePostTags($post, $attr_id, $tags)
    {
        if (!is_array($tags)) {
            throw new \Exception("$attr_id: tag format is invalid.");
        }
        foreach ($tags as $tag_id) {
            $post->valuesPostTag()->create(
                [
                    'post_id' => $post->id,
                    'form_attribute_id' => $attr_id,
                    'tag_id' => $tag_id
                ]
            );
        }
    }
}
