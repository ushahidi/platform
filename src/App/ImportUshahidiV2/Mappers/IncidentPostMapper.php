<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

use League\Flysystem\Util\MimeType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class IncidentPostMapper implements Mapper
{
    const MEDIA_PHOTO = 1;
    const MEDIA_VIDEO = 2;
    const MEDIA_NEWS = 4;
    // These are not actually handled. I haven't seen them in a deployment yet
    const MEDIA_AUDIO = 3;
    const MEDIA_PODCAST = 5;

    protected $mappingRepo;
    protected $attrRepo;

    protected $attributeKeyForColumnCache;
    protected $attributeKeyForFieldCache;
    protected $categoryIdCache;

    public function __construct(ImportMappingRepository $mappingRepo, FormAttributeRepository $attrRepo)
    {
        $this->mappingRepo = $mappingRepo;
        $this->attrRepo = $attrRepo;

        $this->attributeKeyForColumnCache = new Collection();
        $this->attributeKeyForFieldCache = new Collection();
        $this->categoryIdCache = new Collection();
    }

    public function __invoke(int $importId, array $input) : ?Entity
    {
        Log::debug('[IncidentPostMapper] Importing incident {input}', [
            'input' => $input
        ]);
        
        $v3FormId = $this->getFormId($importId, $input['form_id']);
        Log::debug('[IncidentPostMapper] Input form {input_form_id} mapped to {v3_form_id}', [
            'input_form_id' => $input['form_id'],
            'v3_form_id' => $v3FormId
        ]);
        if ($v3FormId == null) {
            /* input form id didn't actually exist */
            return null;
        }

        $v3UserId = $this->mappingRepo->getDestId($importId, 'user', $input['user_id']);

        $postContents = [
            'form_id' => $v3FormId,
            'user_id' => $v3UserId,
            'title' => $input['incident_title'],
            'content' => $input['incident_description'],
            'status' => $input['incident_active'] ? 'published' : 'draft',
            'author_email' => $input['person_email'],
            'author_realname' => $input['person_first'] . ' ' . $input['person_last'],
            'post_date' => $input['incident_date'],
            'values' => $this->getValues($importId, $input, $v3UserId),
            'locale' => 'en_US',
            'type' => 'report',
            'published_to' => [],
        ];
        Log::debug('[IncidentPostMapper] Creating post with contents {postContents}', [
            'postContents' => $postContents
        ]);
        return new Post($postContents);

        // NB: We don't map some data ie:
        // - Custom form fields
    }

    public function getFormId($importId, $formId)
    {
        if ($formId == 0) {
            // Try form id 1 first
            $id = $this->mappingRepo->getDestId($importId, 'form', 1);
            if (!$id) {
                $id = $this->mappingRepo->getDestId($importId, 'form', 0);
            }
            return $id;
        }

        return $this->mappingRepo->getDestId($importId, 'form', $formId);
    }

    public function getAttributeKeyForColumn($importId, $formId, $column)
    {
        Log::debug('[IncidentPostMapper:getAttributeKeyForColumn] Finding attribute key for column {importId},{formId},{column}', [
            'importId' => $importId,
            'formId' => $formId,
            'column' => $column
        ]);

        $cacheKey = serialize([$importId, $formId, $column]);
        if (!$this->attributeKeyForColumnCache->has($cacheKey)) {
            // Get attribute map <formid>-<attribute>
            $id = $this->mappingRepo->getDestId($importId, 'incident_column', $formId.'-'.$column);
            // Load the actual attribute
            $attribute = $this->attrRepo->get($id);
            // Return the key
            $result = $attribute->key ?? $column;
            $this->attributeKeyForColumnCache->put($cacheKey, $result);
        } else {
            $result = $this->attributeKeyForColumnCache->get($cacheKey);
        }

        Log::debug('[IncidentPostMapper:getAttributeKeyForColumn] Finding attribute key result {result}', [
            'result' => $result
        ]);

        return $result;
    }

    public function getAttributeKeyForField($importId, $formId, $field)
    {
        $cacheKey = serialize([$importId, $formId, $field]);
        if (!$this->attributeKeyForFieldCache->contains($cacheKey)) {
            // Get attribute map <formid>-<attribute>
            $id = $this->mappingRepo->getDestId($importId, 'form_field', $field);
            // Load the actual attribute
            $attribute = $this->attrRepo->get($id);
            // Return the key
            $result = $attribute->key ?? $field;
            $this->attributeKeyForFieldCache->put($cacheKey, $result);
        } else {
            $result = $this->attributeKeyForFieldCache->get($cacheKey);
        }
        return $result;
    }

    public function getValues($importId, $input, $userId)
    {
        $values = [
            // @todo handle missing attributes!?
            $this->getAttributeKeyForColumn($importId, $input['form_id'], 'location_name')
                => [$input['location_name']],
            $this->getAttributeKeyForColumn($importId, $input['form_id'], 'location')
                => [['lat' => $input['latitude'], 'lon' => $input['longitude']]],
            $this->getAttributeKeyForColumn($importId, $input['form_id'], 'verified')
                => [$input['incident_verified']],
            // categories
            $this->getAttributeKeyForColumn($importId, $input['form_id'], 'categories') =>
                $this->getCategories($importId, $input['categories']),
            // news_source_link
            $this->getAttributeKeyForColumn($importId, $input['form_id'], 'news_source_link')
                => $this->getMedia($input['media'], self::MEDIA_NEWS, $userId),
            // video_link
            $this->getAttributeKeyForColumn($importId, $input['form_id'], 'video_link')
                => $this->getMedia($input['media'], self::MEDIA_VIDEO, $userId),
            // photos
            $this->getAttributeKeyForColumn($importId, $input['form_id'], 'photos')
                => $this->getMedia($input['media'], self::MEDIA_PHOTO, $userId),
        ];

        if ($input['form_responses']) {
            foreach ($input['form_responses'] as $response) {
                $key = $this->getAttributeKeyForField($importId, $input['form_id'], $response->form_field_id);

                // Add key to values array if not set
                if (!isset($values[$key])) {
                    $values[$key] = [];
                }

                // Convert data type
                list($v3AttrInput, $v3Type) = FormFieldAttributeMapper::getInputAndType(
                    $response->field_type,
                    $response->field_datatype,
                    $response->field_isdate
                );

                Log::debug('Response {id} to {form_field_id} is of type {field_type} -> mapped to {v3AttrInput} and {v3Type}', [
                    'id' => $response->id,
                    'form_field_id' => $response->form_field_id,
                    'field_type' => $response->field_type,
                    'v3AttrInput' => $v3AttrInput,
                    'v3Type' => $v3Type
                ]);

                $value = $this->stringToDatatype($response->form_response, $v3Type);

                // Append the value
                $values[$key][] = $value;
            }
        }

        Log::debug('Importing with values {values}', [
            'values' => $values
        ]);

        return $values;
    }

    public function getCategories($importId, $categories)
    {
        $categories = explode(',', $categories);
        Log::debug('[IncidentPostMapper:getCategories] Starting mapping of categories {categories}', [
            'categories' => $categories
        ]);

        $result = collect($categories)->map(function ($item) use ($importId) {
            return $this->getCategory($importId, $item);
        })->all();
        Log::debug('[IncidentPostMapper:getCategories] Result of category mapping {result}', [
            'result' => $result
        ]);

        return $result;
    }

    public function getCategory($importId, $v2Category)
    {
        $cacheKey = serialize([$importId, $v2Category]);
        if (!$this->categoryIdCache->has($cacheKey)) {
            $result = $this->mappingRepo->getDestId($importId, 'category', $v2Category);
            $this->categoryIdCache->put($cacheKey, $result);
        } else {
            $result = $this->categoryIdCache->get($cacheKey);
        }
        return $result;
    }

    public function getMedia($media, $type, $userId)
    {
        return collect($media)
            ->where('media_type', $type)
            ->map(function ($media) use ($type, $userId) {
                // Not sure what to do with non URL values yet, so just saving them as-is
                // But we probably need to download them based on UrL
                $value = $media->media_link;

                // If this is a photo, save caption too
                if ($type === self::MEDIA_PHOTO) {
                    $extension = pathinfo($value, PATHINFO_EXTENSION);
                    $mimeType = MimeType::detectByFileExtension($extension) ?: 'text/plain';

                    return [
                        'o_filename' => $value,
                        'caption' => $media->media_title,
                        'mime' => $mimeType,
                        // Save with same user id as the post
                        'user_id' => $userId,
                        // Ignoring media_description as I think it's always null
                    ];
                }

                return $value;
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function stringToDatatype($data, $type) {
        switch ($type) {
            case 'text':
            case 'varchar':
                return $data;
            case 'datetime':
                $x = date_parse_from_format("m/d/Y", $data);
                if ($x['error_count'] > 0) {
                    // now what?
                    return "1000-01-01";
                }
                return "{$x['year']}-{$x['month']}-{$x['day']}";
        }
    }
}
