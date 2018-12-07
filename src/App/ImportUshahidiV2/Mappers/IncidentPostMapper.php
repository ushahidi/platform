<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Post;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

class IncidentPostMapper implements Mapper
{
    protected $mappingRepo;

    public function __construct(ImportMappingRepository $mappingRepo)
    {
        $this->mappingRepo = $mappingRepo;
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
            // 'values' => [
            //     $attributeMap['original_id'] => [$item['id']],
            //     $attributeMap['date'] => [$item['incident_date']],
            //     $attributeMap['location_name'] => [$item['location_name']],
            //     $attributeMap['location'] => [[
            //         'lat' => $item['latitude'],
            //         'lon' => $item['longitude']
            //     ]],
            //     $attributeMap['verified'] => [$item['incident_verified']],
            //     // news
            //     // source
            // ]

        ]);

        // NB: We don't map some data ie:
        // - Custom form fields
    }
}
