<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\App\ImportUshahidiV2\Import;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportDataTools;

use League\Flysystem\Util\MimeType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;

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
    protected $dataTools;

    protected $attributeKeyForColumnCache;
    protected $attributeKeyForFieldCache;
    protected $categoryIdCache;
    
    protected $import;
    protected $importTz;

    public function __construct(
        ImportMappingRepository $mappingRepo,
        FormAttributeRepository $attrRepo,
        ImportDataTools $dataTools
    ) {
        $this->mappingRepo = $mappingRepo;
        $this->attrRepo = $attrRepo;
        $this->dataTools = $dataTools;

        $this->attributeKeyForColumnCache = new Collection();
        $this->attributeKeyForFieldCache = new Collection();
        $this->categoryIdCache = new Collection();
    }

    public function __invoke(Import $import, array $input) : ?array
    {
        Log::debug('[IncidentPostMapper] Importing incident {input}', [
            'input' => $input
        ]);
     
        $this->import = $import;
        $importId = $this->import->id;
        $v3FormId = $this->getFormId($importId, $input['form_id']);
        Log::debug('[IncidentPostMapper] Input form {input_form_id} mapped to {v3_form_id}', [
            'input_form_id' => $input['form_id'],
            'v3_form_id' => $v3FormId
        ]);
        if ($v3FormId == null) {
            /* input form id didn't actually exist */
            return null;
        }

        // Pull the timezone the import has been configured with
        $this->importTz = $import->getImportTimezone();
        if (!$this->importTz) {
            $this->importTz = date_default_timezone_get();
            Log::warning('Using host\'s default timezone to read datetime columns (' . $this->importTz .'). '.
              'This may alter the incident creation/update times.');
        }
        Log::info('Converting datetimes to timestamps assuming timezone: ' . $this->importTz);

        $v3UserId = $this->mappingRepo->getDestId($importId, 'user', $input['user_id']);

        $postContents = [
            'form_id' => $v3FormId,
            'user_id' => $v3UserId,
            'title' => $input['incident_title'],
            // V3+ uses TEXT instead of LONGTEXT , so we need to truncate long content
            // TODO: maybe issue a truncation warning when/if it happens?
            'content' => mb_strcut($input['incident_description'], 0, 60000, "UTF-8"),
            'status' => $input['incident_active'] ? 'published' : 'draft',
            'author_email' => $input['person_email'],
            'author_realname' => $input['person_first'] . ' ' . $input['person_last'],
            'locale' => 'en_US',
            'type' => 'report',
            'published_to' => [],
        ];
        $postContents = array_merge($postContents, $this->getPostDateAttributes($input));
        $postContents = array_merge($postContents, [
            'values' => $this->getValues($importId, $input, $v3UserId)
        ]);

        Log::debug('[IncidentPostMapper] Creating post with contents {postContents}', [
            'postContents' => $postContents
        ]);
        return [
            'result' => new Post($postContents)
        ];
    }

    protected function getMysqlDateTimeAsTimestamp($datetime)
    {
        if ($datetime == null) {
            return null;
        }
        try {
            $c = Carbon::createFromFormat('Y-m-d H:i:s', $datetime, $this->importTz);
            return $c->getTimestamp();
        } catch (\Exception $e) {
            Log::info("Expected mysql date, parsing as null", [$datetime]);
            Log::info($e->getMessage());
            return null;
        }
    }

    protected function getPostDateAttributes($input)
    {
        if (array_key_exists('incident_dateadd', $input)) {
            $created = $this->getMysqlDateTimeAsTimestamp($input['incident_dateadd']);
        } else {
            $created = time();
        }

        if (array_key_exists('incident_date', $input) && $input['incident_date'] != null) {
            $incident_date = Carbon::createFromFormat('Y-m-d H:i:s', $input['incident_date'], $this->importTz);
        } else {
            $incident_date = null;
        }

        $dates = [
            'post_date' => $incident_date,
            'created' => $created,
            'updated' => $this->getMysqlDateTimeAsTimestamp($input['incident_datemodify'] ?? null),
        ];

        Log::info("[IncidentPostMapper] using dates: ", [$dates]);
        return $dates;
    }

    public function getFormId($importId, $formId)
    {
        $id = $this->mappingRepo->getDestId($importId, 'form', $formId);
        if (!$id) {
            Log::error("Could not find mapping for v2 form: ", [$formId]);
            return null;
        }
        return $id;
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
            $mappingMeta = $this->mappingRepo->getMetadata($importId, 'form_field', $field);
            // Load the actual attribute
            $attribute = $this->attrRepo->get($id);
            // Return the info
            $result = (object) [
                'attribute' => $this->attrRepo->get($id),
                'mapping_metadata' => $mappingMeta,
            ];
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
            $geometries = $input['geometries'] ? $this->getGeometries($input['geometries']) : [];
            $values[$this->getAttributeKeyForColumn($importId, $input['form_id'], 'geometry')] = $geometries;
        }

        if ($input['form_responses']) {
            foreach ($input['form_responses'] as $response) {
                $mapping = $this->getAttributeForField($importId, $input['form_id'], $response->form_field_id);
                $attr = $mapping->attribute;
                $key = $attr->key;
                // Convert data type
                $v3AttrInput = $attr->input;
                $v3Type = $attr->type;

                // Add key to values array if not set
                if (!isset($values[$key])) {
                    $values[$key] = [];
                }
                
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

                $value = $this->stringToDatatype(
                    $response->form_response,
                    $v3Type,
                    $v3AttrInput,
                    $mapping->mapping_metadata
                );

                // Append the value
                if ($value !== '') {
                    $values[$key][] = $value;
                }
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
                // If this is a photo, save caption too
                if ($type === self::MEDIA_PHOTO) {
                    $extension = pathinfo($media->media_link, PATHINFO_EXTENSION);
                    $mimeType = MimeType::detectByFileExtension(strtolower($extension)) ?: 'text/plain';

                    return [
                        'o_filename' => $media->media_link,
                        'caption' => $media->media_title,
                        'mime' => $mimeType,
                        // Save with same user id as the post
                        'user_id' => $userId,
                        // Ignoring media_description as I think it's always null
                    ];
                } elseif ($type === self::MEDIA_VIDEO) {
                    // Normalize youtube URLs
                    $youtube_matches = [
                        '/^https?:\/\/((www|m)\.)?youtube.com(\/)?\?(.*&)?vi?=(?P<vid>[a-zA-Z0-9_\-]+)/',
                        '/^https?:\/\/((www|m)\.)?youtube.com\/' .
                            '(watch|ytscreeningroom)\?(.*&)?vi?=(?P<vid>[a-zA-Z0-9_\-]+)/',
                        '/^https?:\/\/((www|m)\.)?youtube.com\/(v|e|vi|embed)\/(?P<vid>[a-zA-Z0-9_\-]+)/',
                        '/^https?:\/\/youtu.be\/(?P<vid>[a-zA-Z0-9_\-]+)/',
                    ];
                    foreach ($youtube_matches as $re) {
                        $matches = [];
                        if (preg_match($re, $media->media_link, $matches)) {
                            return "https://www.youtube.com/embed/" . $matches['vid'];
                        }
                    }
                }
                return $media->media_link;
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

    protected function stringToDatatype($data, $type, $attrInput, $mappingMeta = [])
    {
        switch ($type) {
            case 'text':
            case 'varchar':
                if ($attrInput == "checkboxes") {
                    // v3+ now JSON-encodes the selected checkbox values
                    return json_encode(array_filter(array_map("trim", explode(",", $data))));
                } else {
                    return $data;
                }
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
                return $this->stringToDate($data, $mappingMeta);
        }
    }

    protected function stringToDate($data, $mappingMeta)
    {
        if ($mappingMeta['decode']['datetime']['format_study'] ?? false) {
            $formats = $mappingMeta['decode']['datetime']['format_study'];
            // expecting array of [ 'format' => ... , 'score' => ... ] sub-arrays
            $formats = array_map(function ($f) {
                return $f['format'];
            }, $formats);
        } else {
            $formats = ["d#m#Y", "m#d#Y"];
        }
        foreach ($formats as $f) {
            $x = date_parse_from_format($f, trim($data));
            if (($x['error_count'] + $x['warning_count']) == 0) {
                return "{$x['year']}-{$x['month']}-{$x['day']}";
            }
        }
        Log::error("Unparseable date string: ", [$data]);
        return '';
    }
}
