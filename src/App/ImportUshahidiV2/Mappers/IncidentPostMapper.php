<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use League\Flysystem\Util\MimeType;

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

    public function __construct(ImportMappingRepository $mappingRepo, FormAttributeRepository $attrRepo)
    {
        $this->mappingRepo = $mappingRepo;
        $this->attrRepo = $attrRepo;
    }

    public function __invoke(int $importId, array $input) : Entity
    {
        $user_id = $this->mappingRepo->getDestId($importId, 'user', $input['user_id']);
        return new Post([
            'form_id' => $this->mappingRepo->getDestId($importId, 'form', $input['form_id']),
            'user_id' => $user_id,
            'title' => $input['incident_title'],
            'content' => $input['incident_description'],
            'status' => $input['incident_active'] ? 'published' : 'draft',
            'author_email' => $input['person_email'],
            'author_realname' => $input['person_first'] . ' ' . $input['person_last'],
            'post_date' => $input['incident_date'],
            'values' => [
                // @todo handle missing attributes!?
                $this->getAttributeKey($importId, $input['form_id'], 'location_name')
                    => [$input['location_name']],
                $this->getAttributeKey($importId, $input['form_id'], 'location')
                    => [['lat' => $input['latitude'], 'lon' => $input['longitude']]],
                $this->getAttributeKey($importId, $input['form_id'], 'verified')
                    => [$input['incident_verified']],
                // categories
                $this->getAttributeKey($importId, $input['form_id'], 'categories') =>
                    $this->getCategories($importId, $input['categories']),
                // news_source_link
                $this->getAttributeKey($importId, $input['form_id'], 'news_source_link')
                    => $this->getMedia($input['media'], self::MEDIA_NEWS, $user_id),
                // video_link
                $this->getAttributeKey($importId, $input['form_id'], 'video_link')
                    => $this->getMedia($input['media'], self::MEDIA_VIDEO, $user_id),
                // photos
                $this->getAttributeKey($importId, $input['form_id'], 'photos')
                    => $this->getMedia($input['media'], self::MEDIA_PHOTO, $user_id),
            ],
            'locale' => 'en_US',
            'type' => 'report',
            'published_to' => [],
        ]);

        // NB: We don't map some data ie:
        // - Custom form fields
    }

    public function getAttributeKey($importId, $formId, $column)
    {
        // Get attribute map <formid>-<attribute>
        $id = $this->mappingRepo->getDestId($importId, 'incident_column', $formId.'-'.$column);
        // Load the actual attribute
        $attribute = $this->attrRepo->get($id);
        // Return the key
        return $attribute->key ?? $column;
    }

    public function getCategories($importId, $categories)
    {
        $categories = explode(',', $categories);

        return collect($categories)->map(function ($item) use ($importId) {
            return $this->mappingRepo->getDestId($importId, 'category', $item);
        })->all();
    }

    public function getMedia($media, $type, $user_id)
    {
        return collect($media)
            ->where('media_type', $type)
            ->map(function ($media) use ($type, $user_id) {
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
                        'user_id' => $user_id,
                        // Ignoring media_description as I think it's always null
                    ];
                }

                return $value;
            })
            ->filter()
            ->values()
            ->all();
    }
}
