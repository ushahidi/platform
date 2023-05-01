<?php

declare(strict_types=1);

namespace Ushahidi\Addons\Rackspace\Identity;

use OpenStack\Common\Api\ApiInterface;

/**
 * Represents the OpenStack Identity v2 API.
 *
 * Based of https://github.com/php-opencloud/openstack/issues/316#issuecomment-722656643
 */
class Api implements ApiInterface
{
    public function postToken(): array
    {
        return [
            'method' => 'POST',
            'path'   => 'tokens',
            'params' => [
                'username' => [
                    'type'     => 'string',
                    'required' => true,
                    'path'     => 'auth.RAX-KSKEY:apiKeyCredentials',
                ],
                'apiKey' => [
                    'type'     => 'string',
                    'required' => true,
                    'path'     => 'auth.RAX-KSKEY:apiKeyCredentials',
                ],
            ],
        ];
    }
}
