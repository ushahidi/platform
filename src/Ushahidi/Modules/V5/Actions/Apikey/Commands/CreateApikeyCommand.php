<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Commands;

use Ramsey\Uuid\Uuid;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Requests\ApiKeyRequest;
use Ushahidi\Core\Entity\ApiKey as ApikeyEntity;

class CreateApikeyCommand implements Command
{
    /**
     * @var ApikeyEntity
     */
    private $apikey_entity;


    

    public function __construct(ApikeyEntity $apikey_entity)
    {
        $this->apikey_entity = $apikey_entity;
    }

    public static function fromRequest(ApiKeyRequest $request): self
    {
        $uuid = Uuid::uuid4();
        $input['api_key'] = $uuid->toString();
        $input['client_id'] = $request->input('client_id');
        $input['client_secret'] = $request->input('client_secret');
        $input['created'] = time();
        $input['updated'] = null;
    // Note : client id/secret ared isabled from the entity
        return new self(new ApikeyEntity($input));
    }

    /**
     * @return ApikeyEntity
     */
    public function getApikeyEntity(): ApikeyEntity
    {
        return $this->apikey_entity;
    }
}
