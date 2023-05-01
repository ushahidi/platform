<?php
namespace Ushahidi\Addons\Rackspace\CDN;

use OpenStack\Common\Api\ApiInterface;

class Api implements ApiInterface
{
    public function headContainer(): array
    {
        return [
            'method' => 'HEAD',
            'path'   => '{name}',
            'params' => [
                'name' => [
                    'location'    => 'url',
                    'required'    => true,
                ]
            ],
        ];
    }
}
