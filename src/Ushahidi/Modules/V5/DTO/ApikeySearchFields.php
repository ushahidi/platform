<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class ApikeySearchFields
{
    /**
     * @var ?string
     */
    private $apikey;
    private $type;
    private $user;
    private $data_source;
    public function __construct(Request $request)
    {
        $this->apikey = $request->query('apikey');
        $this->type = $request->query('type');
        $this->data_source = $request->query('data_source');
        
        $this->user = $request->query('user');

        if ($request->get('user') == 'me' || !ParameterUtilities::checkIfUserAdmin()) {
                $this->user = [Auth::id()];
        } else {
            $this->user = ParameterUtilities::getParameterAsArray($request->get('user'));
        }
    }

    public function apikey()
    {
        return $this->apikey;
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
