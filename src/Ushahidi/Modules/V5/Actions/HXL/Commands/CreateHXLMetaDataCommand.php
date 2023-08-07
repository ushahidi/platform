<?php

namespace Ushahidi\Modules\V5\Actions\HXL\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\HXL\HXLMetadata as HXLMetadataEntity;
use Ushahidi\Modules\V5\Models\Stage;
use Ushahidi\Modules\V5\Requests\HXLMetadataRequest;

class CreateHXLMetaDataCommand implements Command
{
    /**
     * @var HXLMetadataEntity
     */
    private $hxl_metdata_entity;


    

    public function __construct(HXLMetadataEntity $hxl_metdata_entity)
    {
        $this->hxl_metdata_entity = $hxl_metdata_entity;
    }

    public static function fromRequest(HXLMetadataRequest $request): self
    {

        $user = Auth::user();
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['private'] = $request->input('private');
        $input['dataset_title'] = $request->input('dataset_title');
        $input['license_id'] = $request->input('license_id');
        $input['organisation_id'] = $request->input('organisation_id');
        $input['organisation_name'] = $request->input('organisation_name');
        $input['source'] = $request->input('source');
        $input['created'] = time();
        $input['updated'] = null;

        return new self(new HXLMetadataEntity($input));
    }

    /**
     * @return HXLMetadataEntity
     */
    public function getHXLMetadataEntity(): HXLMetadataEntity
    {
        return $this->hxl_metdata_entity;
    }
}
