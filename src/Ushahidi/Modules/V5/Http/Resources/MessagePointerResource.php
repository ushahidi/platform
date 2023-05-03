<?php
namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class MessagePointerResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => route('messages.show', [ 'id' => $this->id ])
        ];
    }
}
