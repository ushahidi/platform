<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class ExportJobSearchFields
{
    /**
     * @var ?string
     */
    private $max_expiration;
    private $entity_type;
    private $user;
    public function __construct(Request $request)
    {
        $this->max_expiration = $request->query('max_expiration');
        $this->entity_type = $request->query('entity_type');
        
        $this->user = $request->query('user');
        if ($request->get('user') == 'me' || !ParameterUtilities::checkIfUserAdmin()) {
                $this->user = [Auth::id()];
        } else {
            $this->user = ParameterUtilities::getParameterAsArray($request->get('user'));
        }
    }

    public function maxExpiration()
    {
        return $this->max_expiration;
    }

    public function entityType()
    {
        return $this->entity_type;
    }

    public function user()
    {
        return $this->user;
    }
}
