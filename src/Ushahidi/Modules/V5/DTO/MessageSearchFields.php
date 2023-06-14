<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class MessageSearchFields
{
    /**
     * @var ?string
     */
    private $query;
    private $type;
    private $box;
    private $status;
    private $contact;
    private $parent;
    private $post;
    private $data_source;
    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->box = $request->query('box');
        $this->status = $request->query('status');
        $this->contact = $request->query('contact');
        $this->parent = $request->query('parent');
        $this->post = $request->query('post');
        $this->type = $request->query('type');
        $this->data_source = $request->query('data_source');
    }

    public function q()
    {
        return $this->query;
    }

    public function type()
    {
        return $this->type;
    }

    public function box()
    {
        return $this->box;
    }

    public function contact()
    {
        return $this->contact;
    }

    public function status()
    {
        return $this->status;
    }

    public function parent()
    {
        return $this->parent;
    }

    public function post()
    {
        return $this->post;
    }

    public function dataSource()
    {
        return $this->data_source;
    }
}
