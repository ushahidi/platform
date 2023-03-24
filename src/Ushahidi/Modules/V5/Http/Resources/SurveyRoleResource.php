<?php
namespace Ushahidi\Modules\V5\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Ushahidi\Modules\V5\Models\SurveyRole;

class SurveyRoleResource extends BaseResource
{
    public static $wrap = 'result';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "form_id" => $this->form_id,
            "role_id" => $this->role_id
        ];
    }
}
