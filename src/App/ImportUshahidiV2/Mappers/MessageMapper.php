<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

class MessageMapper implements Mapper
{
    protected $mappingRepo;
    protected $contactRepo;

    public function __construct(ImportMappingRepository $mappingRepo, ContactRepository $contactRepo)
    {
        $this->mappingRepo = $mappingRepo;
        $this->contactRepo = $contactRepo;
    }

    public function __invoke(int $importId, array $input) : Entity
    {
        // We are not mapping some fields
        // - message_level
        // - message_type = 3 (DELETED)
        // - message_from
        // - message_to

        return new Message([
            'contact_id' => $this->getContactId($input['service_account'], $this->getType($input['service_name'])),
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
    }

    protected function getType($serviceName)
    {
        // This handles sms, twitter, email and other not yet supporter services
        return strtolower($serviceName);
    }

    protected function getDataSource($serviceName)
    {
        if ($serviceName === 'SMS') {
            // Could be many sources
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

    protected function getContactId($reporterServiceAccount, $type)
    {
        // Rather than use the mapping repo, just search by the contact info itself
        $contact = $this->contactRepo->getByContact($reporterServiceAccount, $type);

        return $contact->id;
    }

    protected function getParentId($importId, $parentId)
    {
        return $this->mappingRepo->getDestId($importId, 'message', $parentId);
    }

    protected function getUserId($importId, $userId)
    {
        return $this->mappingRepo->getDestId($importId, 'user', $userId);
    }

    protected function getPostId($importId, $incidentId)
    {
        return $this->mappingRepo->getDestId($importId, 'incident', $incidentId);
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
