<?php

namespace Ushahidi\App\Http\Controllers\API;

use Ushahidi\App\Http\Controllers\RESTController;

class ConfigController extends RESTController
{

    // protected $_action_map = array
    // (
    //     Http_Request::GET     => 'get',
    //     Http_Request::PUT     => 'put', // Typically Update..
    //     Http_Request::OPTIONS => 'options',
    // );

    protected function getScope()
    {
        return 'config';
    }

    // protected function _is_auth_required()
    // {
    //     if (parent::_is_auth_required())
    //     {
    //         // Completely anonymous access is allowed for (some) GET requests.
    //         // Further checks are made down the stack.
    //         return ($this->request->method() !== Request::GET);
    //     }
    //     return FALSE;
    // }

}
