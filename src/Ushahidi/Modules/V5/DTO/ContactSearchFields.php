<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactSearchFields
{
    /**
     * @var ?string
     */
    private $contact;
    private $type;
    private $user;
    private $data_source;
    public function __construct(Request $request)
    {
        $this->contact = $request->query('contact');
        $this->type = $request->query('type');
        $this->data_source = $request->query('data_source');
        
        $this->user = $request->query('user');
    }

    public function contact()
    {
        return $this->contact;
    }

    public function type()
    {
        return $this->type;
    }

    public function user()
    {
        return $this->user;
    }

    public function dataSource()
    {
        return $this->data_source;
    }
}
