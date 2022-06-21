<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\App\ImportUshahidiV2\Import;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ReporterUserMapper implements Mapper
{

    protected $mappingRepo;
    protected $userMappingsCache;

    protected function loadUserMappings(int $importId) : Collection
    {
        if (!$this->userMappingsCache->has($importId)) {
            $mappings = $this->mappingRepo->getAllMappingIDs($importId, "user");
            $this->userMappingsCache->put($importId, $mappings);
            #
            Log::debug('[ReporterUserMapper] Loaded user ID mappings for {importId}: {mappings}', [
                'importId' => $importId,
                'mappings' => $mappings
            ]);
        }
        return $this->userMappingsCache->get($importId);
    }

    public function __construct(ImportMappingRepository $mappingRepo)
    {
        $this->mappingRepo = $mappingRepo;
        $this->userMappingsCache = new Collection();
    }

    public function __invoke(Import $import, array $input) : array
    {
        // NB:
        // - We're not mapping level to anything
        // - We're not mapping reporter location to anything
        $userIdMap = $this->loadUserMappings($import->id);

        $contacts = $this->getContacts($input, $userIdMap);

        return [
            'result' => new Contact($contacts)
        ];
    }

    protected function getContacts($input, $userIdMap) : array
    {
        $contacts = [];

        // reporter_phone is *never* set, so we can ignore it
        // reporter_email is ever set to the same thing as service_account, so we can ignore it
        
        if ($input['service_name'] === 'SMS') {
            $contacts = [
                'type' => 'phone',
                // filling this as null since v2 didn't keep track of it
                'data_source' => null,
                'contact' => $input['service_account'],
                'can_notify' => true,
            ];
        } else {
            // This handles twitter, email and other not yet supporter services
            $contacts = [
                'type' => strtolower($input['service_name']),
                'data_source' => strtolower($input['service_name']),
                'contact' => $input['service_account'],
                'can_notify' => true,
            ];
        }
        
        // map the user id to its already existing corresponding id
        $contacts['user_id'] = $userIdMap->get(strval($input['user_id']));

        Log::debug('[ReporterUserMapper] Mapped input {input} to contact {contacts}', [
            'input' => $input,
            'contacts' => $contacts
        ]);

        return $contacts;
    }

    protected function getName($first, $last)
    {
        return implode(' ', array_filter([$first, $last]));
    }
}
