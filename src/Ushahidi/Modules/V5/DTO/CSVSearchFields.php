<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class CSVSearchFields
{
    /**
     * @var array
     */
    private $columns;
    private $maps_to;
    private $fixed;
    private $file_name;
    public function __construct(Request $request)
    {
        $this->columns = ParameterUtilities::getParameterAsArray($request->get('columns'));
        $this->maps_to = $request->query('maps_to');
        $this->fixed = $request->query('fixed');
        $this->file_name = $request->query('file_name');
    }

    public function columns():array
    {
        return $this->columns;
    }

    public function mapsTo()
    {
        return $this->maps_to;
    }

    public function fixed()
    {
        return $this->fixed;
    }

    public function fileName()
    {
        return $this->file_name;
    }
}
