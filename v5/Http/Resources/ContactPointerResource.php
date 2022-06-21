<?php
namespace v5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ContactPointerResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => route('contacts.show', [ 'id' => $this->id ])
        ];
    }
}
