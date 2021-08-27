<?php
namespace v5\Http\Resources;

class MessagePointerResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => route('messages.show', [ 'id' => $this->id ])
        ];
    }
}
