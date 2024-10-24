<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class WebhookSearchFields extends SearchFields
{
    
    private $user;
    public function __construct(Request $request)
    {
        $this->user = $request->query('user');

        if ($request->get('user') == 'me') {
                $this->user = [Auth::id()];
        } else {
            $this->user = ParameterUtilities::getParameterAsArray($request->get('user'));
        }
    }

    public function user()
    {
        return $this->user;
    }
}
