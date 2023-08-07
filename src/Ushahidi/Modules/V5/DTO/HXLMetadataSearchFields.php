<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class HXLMetadataSearchFields
{
    
    private $user;
    private $dataset_title;
    public function __construct(Request $request)
    {
        $this->user = $request->query('user');

        if ($request->get('user') == 'me') {
                $this->user = [Auth::id()];
        } else {
            $this->user = ParameterUtilities::getParameterAsArray($request->get('user'));
        }

        $this->dataset_title = $request->query('dataset_title');
    }

    public function user()
    {
        return $this->user;
    }

    public function datasetTitle()
    {
        return $this->dataset_title;
    }
}
