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
            'content' => mb_strcut($input['incident_description'], 0, 60000, "UTF-8"),
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
        Log::debug(
            '[IncidentPostMapper:getAttributeKeyForColumn] '.
            'Finding attribute key for column {importId},{formId},{column}',
            [
                'importId' => $importId,
                'formId' => $formId,
                'column' => $column
            ]
        );

        $cacheKey = serialize([$importId, $formId, $column]);
        if (!$this->attributeKeyForColumnCache->has($cacheKey)) {
            // Get attribute map <formid>-<attribute>
            $id = $this->mappingRepo->getDestId($importId, 'incident_column', $formId.'-'.$column);
            if ($id) {
                // Load the actual attribute
                $attribute = $this->attrRepo->get($id);
                // Return the key
                $result = $attribute->key ?? $column;
            } else {
                $result = null;
            }
            $this->attributeKeyForColumnCache->put($cacheKey, $result);
        } else {
            $result = $this->attributeKeyForColumnCache->get($cacheKey);
        }

        Log::debug('[IncidentPostMapper:getAttributeKeyForColumn] Finding attribute key result {result}', [
            'result' => $result
        ]);

        return $result;
    }

    public function getAttributeForField($importId, $formId, $field)
    {
        $cacheKey = serialize([$importId, $formId, $field]);
        if (!$this->attributeKeyForFieldCache->contains($cacheKey)) {
            // Get attribute map <formid>-<attribute>
            $id = $this->mappingRepo->getDestId($importId, 'form_field', $field);
            // Load the actual attribute
            $attribute = $this->attrRepo->get($id);
            // Return the info
            $result = $attribute;
            $this->attributeKeyForFieldCache->put($cacheKey, $result);
        } else {
            $result = $this->attributeKeyForFieldCache->get($cacheKey);
        }

        Log::debug('Mapping v2 attribute field with id {v2FieldId} with result {result}', [
            'v2FieldId' => $field,
            'result' => $result
        ]);

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

        if ($this->getAttributeKeyForColumn($importId, $input['form_id'], 'geometry')) {
            // What kind of processing needs to be done here? not sure yet
            $values[$this->getAttributeKeyForColumn($importId, $input['form_id'], 'geometry')] =
                $input['geometries'] ? $this->getGeometries($input['geometries']) : [];
        }

        if ($input['form_responses']) {
            foreach ($input['form_responses'] as $response) {
                $attr = $this->getAttributeForField($importId, $input['form_id'], $response->form_field_id);
                $key = $attr->key;
                // Convert data type
                $v3AttrInput = $attr->input;
                $v3Type = $attr->type;

                // Add key to values array if not set
                if (!isset($values[$key])) {
                    $values[$key] = [];
                }

                // list($v3AttrInput, $v3Type) = FormFieldAttributeMapper::getInputAndType(
                //     $response->field_type,
                //     $response->field_datatype,
                //     $response->field_isdate
                // );

                Log::debug(
                    'Response {id} to {form_field_id} is of type {field_type} -> '.
                    'mapped to {v3AttrInput} and {v3Type} with key {v3Key}',
                    [
                        'id' => $response->id,
                        'form_field_id' => $response->form_field_id,
                        'field_type' => $response->field_type,
                        'v3AttrInput' => $v3AttrInput,
                        'v3Type' => $v3Type,
                        'v3Key' => $key
                    ]
                );

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
        if ($categories == null) {
            return [];
        }
        
        $categories = array_filter(explode(',', $categories));
        Log::debug('[IncidentPostMapper:getCategories] Starting mapping of categories {categories}', [
            'categories' => $categories
        ]);

        $result = collect($categories)->map(function ($item) use ($importId) {
            return $this->getCategory($importId, $item);
        })->unique()->all();
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
                    $mimeType = MimeType::detectByFileExtension(strtolower($extension)) ?: 'text/plain';

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

    public function getGeometries(Collection $geometries)
    {
        Log::debug('[IncidentPostMapper] Processing geometries {$geometries}', [
            'geometries' => $geometries
        ]);
        $the_texts = collect($geometries)->pluck('geometry_astext')->all();
        Log::debug('[IncidentPostMapper] Processing geometries result: {$text}', [
            'text' => $the_texts,
        ]);
        return $the_texts;
    }

    protected function stringToDatatype($data, $type)
    {
        switch ($type) {
            case 'text':
            case 'varchar':
                return $data;
            case 'integer':
                if (is_numeric(trim($data))) {
                    return intval(trim($data));
                } else {
                    return null;
                }
            case 'decimal':
                if (is_numeric(trim($data))) {
                    return floatval(trim($data));
                } else {
                    return null;
                }
            case 'datetime':
                if (trim($data) === '') {
                    return '';
                }
                $x = date_parse_from_format("d/m/Y", trim($data));
                if ($x['error_count'] > 0) {
                    Log::error("Unparseable date string: ", [$x]);
                    return '';
                }
                return "{$x['year']}-{$x['month']}-{$x['day']}";
        }
    }
}
