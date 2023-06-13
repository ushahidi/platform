<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class NotificationSearchFields
{

    private $user;
    private $set;
    public function __construct(Request $request)
    {
        $this->user = $request->query('user');
        if ($request->get('user') == 'me' || !ParameterUtilities::checkIfUserAdmin()) {
                $this->user = [Auth::id()];
        } else {
            $this->user = ParameterUtilities::getParameterAsArray($request->get('user'));
        }

        $this->set = ParameterUtilities::getParameterAsArray($request->get('set'));
    }

    public function user()
    {
        return $this->user;
    }

    public function set()
    {
        return $this->set;
    }
}
