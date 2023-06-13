<?php

namespace Ushahidi\Modules\V5\Actions\Layer\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Layer;
use Ushahidi\Modules\V5\Requests\LayerRequest;
use Ushahidi\Core\Entity\Layer as LayerEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateLayerCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var LayerEntity
     */
    private $layer_entity;

    public function __construct(
        int $id,
        LayerEntity $layer_entity
    ) {
        $this->id = $id;
        $this->layer_entity = $layer_entity;
    }

    public static function fromRequest(int $id, LayerRequest $request, Layer $current_layer): self
    {
        $input['media_id'] = $request->input('media_id') ?? $current_layer->media_id;
        $input['name'] = $request->input('name')?$request->input('name'):$current_layer->name;
        $input['type'] = $request->input('type')?$request->input('type'):$current_layer->type;
        $input['data_url'] = $request->input('data_url')?$request->input('data_url'):$current_layer->data_url;
        $input['options'] = $request->input('options')?$request->input('options'):$current_layer->options;
        $input['active'] = $request->input('active')?$request->input('active'):$current_layer->active;
        $input['visible_by_default'] = $request->input('visible_by_default')??$current_layer->visible_by_default;
        $input['created'] = strtotime($current_layer->created);
        $input['updated'] = time();

        return new self($id, new LayerEntity($input));
    }
    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return LayerEntity
     */
    public function getLayerEntity(): LayerEntity
    {
        return $this->layer_entity;
    }
}
