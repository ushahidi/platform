<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;

class ReporterUserMapper implements Mapper
{
    public function __invoke(int $importId, array $input) : Entity
    {
        // NB:
        // - We're not mapping level to anything
        // - We're not mapping reporter location to anything

        // @todo check for duplicates from users or previous reporters

        return new User([
            'email' => $input['service_name'] === 'Email' ? $input['service_account'] : $input['reporter_email'],
            'realname' => $this->getName($input['reporter_first'], $input['reporter_last']),
            'role' => null, // Reporters don't have any log in role at all
            'contacts' => $this->getContacts($input),
        ]);
    }

    protected function getContacts($input)
    {
        $contacts = [];

        // reporter_phone is *never* set, so we can ignore it
        // reporter_email is ever set to the same thing as service_account, so we can ignore it

        if ($input['service_name'] === 'SMS') {
            $contacts[] = [
                'type' => 'phone',
                'data_source' => null,
                'contact' => $input['service_account'],
                'can_notify' => true,
            ];
        } else {
            // This handles twitter, email and other not yet supporter services
            $contacts[] = [
                'type' => strtolower($input['service_name']),
                'data_source' => strtolower($input['service_name']),
                'contact' => $input['service_account'],
                'can_notify' => true,
            ];
        }

        return $contacts;
    }

    protected function getName($first, $last)
    {
        return implode(' ', array_filter([$first, $last]));
    }
}
