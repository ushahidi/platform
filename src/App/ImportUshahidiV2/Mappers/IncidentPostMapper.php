<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

class IncidentPostMapper implements Mapper
{
    protected $mappingRepo;
    protected $attrRepo;

    public function __construct(ImportMappingRepository $mappingRepo, FormAttributeRepository $attrRepo)
    {
        $this->mappingRepo = $mappingRepo;
        $this->attrRepo = $attrRepo;
    }

    public function __invoke(array $input) : Entity
    {
        return new Post([
            'form_id' => $this->mappingRepo->getDestId('form', $input['form_id']),
            'user_id' => $this->mappingRepo->getDestId('user', $input['user_id']),
            'title' => $input['incident_title'],
            'content' => $input['incident_description'],
            'status' => $input['incident_active'] ? 'published' : 'draft',
            'author_email' => $input['person_email'],
            'author_realname' => $input['person_first'] . ' ' . $input['person_last'],
            'post_date' => $input['incident_date'],
            'values' => [
                // @todo handle missing attributes!?
                $this->getAttributeKey($input['form_id'], 'location_name')
                    => [$input['location_name']],
                $this->getAttributeKey($input['form_id'], 'location')
                    => [['lat' => $input['latitude'], 'lon' => $input['longitude']]],
                $this->getAttributeKey($input['form_id'], 'verified')
                    => [$input['incident_verified']],
                // news_source_link
                // video_link
                // photos
                // categories
            ]
        ]);

        // NB: We don't map some data ie:
        // - Custom form fields
    }

    public function getAttributeKey($formId, $column)
    {
        // Get attribute map <formid>-<attribute>
        $id = $this->mappingRepo->getDestId('incident_column', $formId.'-'.$column);
        // Load the actual attribute
        $attribute = $this->attrRepo->get($id);
        // Return the key
        return $attribute->key ?? $column;
    }
}
