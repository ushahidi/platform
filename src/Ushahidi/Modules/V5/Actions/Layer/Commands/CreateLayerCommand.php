<?php

namespace Ushahidi\Modules\V5\Actions\Layer\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Layer;
use Ushahidi\Modules\V5\Requests\LayerRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Ohanzee\Entities\Layer as LayerEntity;
use Ushahidi\Modules\V5\Models\Stage;

class CreateLayerCommand implements Command
{
    /**
     * @var LayerEntity
     */
    private $layer_entity;


    

    public function __construct(LayerEntity $layer_entity)
    {
        $this->layer_entity = $layer_entity;
    }

    public static function fromRequest(LayerRequest $request): self
    {

        $input['media_id'] = $request->input('media_id');
        $input['name'] = $request->input('name');
        $input['type'] = $request->input('type');
        $input['data_url'] = $request->input('data_url');
        $input['options'] = $request->input('options');
        $input['active'] = $request->input('active');
        $input['visible_by_default'] = $request->input('visible_by_default');
        $input['created'] = time();
        $input['updated'] = time();

        return new self(new LayerEntity($input));
    }

    /**
     * @return LayerEntity
     */
    public function getLayerEntity(): LayerEntity
    {
        return $this->layer_entity;
    }
}
