<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\App\ImportUshahidiV2\Import;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MessageMapper implements Mapper
{
    protected $mappingRepo;
    protected $contactRepo;

    protected $mappingsCache;

    protected function loadMappings(int $importId, string $type) : Collection
    {
        $importCache = $this->mappingsCache->get($type);
        
        if (!$importCache->has($importId)) {
            $mappings = $this->mappingRepo->getAllMappingIDs($importId, $type);
            $importCache->put($importId, $mappings);
        }
        return $importCache->get($importId);
    }

    public function __construct(ImportMappingRepository $mappingRepo, ContactRepository $contactRepo)
    {
        $this->mappingRepo = $mappingRepo;
        $this->contactRepo = $contactRepo;
        $this->mappingsCache = new Collection([
            'reporter' => new Collection(),
            'user' => new Collection(),
            'incident' => new Collection(),
            'message' => new Collection()
        ]);
    }

    public function __invoke(Import $import, array $input) : array
    {
        // We are not mapping some fields
        // - message_level
        // - message_type = 3 (DELETED)
        // - message_from
        // - message_to
        $importId = $import->id;
        $result = new Message([
            'contact_id' => $this->getContactId($importId, $input['reporter_id']),
            'parent_id' => $this->getParentId($importId, $input['parent_id']),
            'post_id' => $this->getPostId($importId, $input['incident_id']),
            'user_id' => $this->getUserId($importId, $input['user_id']),
            'data_source' => $this->getDataSource($input['service_name']),
            'data_source_message_id' => $input['service_messageid'],
             // It's possible message could be truncated because message was TEXT but title is VARCHAR
            'title' => empty($input['message_detail']) ? null : $input['message'],
            'message' => empty($input['message_detail']) ? $input['message'] : $input['message_detail'],
            'datetime' => $input['message_date'],
            'type' => $this->getType($input['service_name']),
            'status' => $this->getStatus($input['message_type']),
            'direction' => $this->getDirection($input['message_type']),
            'additional_data' => $this->getAdditionalData($input),
        ]);

        return [
            'result' => $result
        ];
    }

    protected function getType($serviceName)
    {
        // This handles sms, twitter, email and other not yet supporter services
        return strtolower($serviceName);
    }

    protected function getDataSource($serviceName)
    {
        if ($serviceName === 'SMS') {
            // Could be many sources, v2 didn't track the specific source
            return null;
        } else {
            // This handles twitter, email and other not yet supporter services
            return strtolower($serviceName);
        }
    }

    protected function getDirection($type)
    {
        return $type == 2 ? 'outgoing' : 'incoming';
    }

    protected function getStatus($type)
    {
        return $this->getDirection($type) == 'incoming' ? 'received' : 'sent';
    }

    protected function getContactId($importId, $contact_id)
    {
        return $this->loadMappings($importId, 'reporter')->get(strval($contact_id));
    }

    protected function getParentId($importId, $parentId)
    {
        return $this->loadMappings($importId, 'message')->get(strval($parentId));
    }

    protected function getUserId($importId, $userId)
    {
        return $this->loadMappings($importId, 'user')->get(strval($userId));
    }

    protected function getPostId($importId, $incidentId)
    {
        return $this->loadMappings($importId, 'incident')->get(strval($incidentId));
    }

    protected function getAdditionalData($input)
    {
        if ($input['longitude'] && $input['latitude']) {
            return [
                'location' => [[
                    'type' => 'Point',
                    'coordinates' => [
                        (float) $input['longitude'], // lon
                        (float) $input['latitude'],  // lat
                    ]
                ]],
            ];
        }

        return null;
    }
}
