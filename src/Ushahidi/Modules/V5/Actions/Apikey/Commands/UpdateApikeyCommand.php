<?php

namespace Ushahidi\Modules\V5\Actions\Apikey\Commands;

use Ramsey\Uuid\Uuid;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Apikey;
use Ushahidi\Modules\V5\Requests\ApiKeyRequest;
use Ushahidi\Core\Entity\ApiKey as ApikeyEntity;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateApikeyCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var ApikeyEntity
     */
    private $apikey_entity;

    public function __construct(
        int $id,
        ApikeyEntity $apikey_entity
    ) {
        $this->id = $id;
        $this->apikey_entity = $apikey_entity;
    }

    public static function fromRequest(int $id, ApiKeyRequest $request, Apikey $current_apikey): self
    {
        $uuid = Uuid::uuid4();
        $input['api_key'] = $uuid->toString();
        $input['client_id'] = $request->input('client_id')?? $current_apikey->client_id;
        $input['client_secret'] = $request->input('client_secret') ?? $current_apikey->client_secret;
        $input['created'] = strtotime($current_apikey->created);
        $input['updated'] = time();
        return new self($id, new ApikeyEntity($input));
    }

    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return ApikeyEntity
     */
    public function getApikeyEntity(): ApikeyEntity
    {
        return $this->apikey_entity;
    }
}
