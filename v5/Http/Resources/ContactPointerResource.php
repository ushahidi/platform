<?php
namespace v5\Http\Resources;

class ContactPointerResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => route('contacts.show', [ 'id' => $this->id ])
        ];
    }
}
