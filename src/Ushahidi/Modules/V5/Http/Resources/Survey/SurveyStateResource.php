<?php
namespace Ushahidi\Modules\V5\Http\Resources\Survey;

use Illuminate\Http\Resources\Json\Resource;

class SurveyStateResource extends Resource
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
        $data = json_decode($this->toJson(), true);
        return [
            'total_responses' => $data['total_responses'],
            'total_recipients' => $data['total_recipients'],
            'total_response_recipients' => $data['total_response_recipients'],
            'total_messages_sent' => $data['total_messages_sent'],
            'total_messages_pending' => $data['total_messages_pending'],
            'total_by_data_source' => $data['total_by_data_source'],
        ];
    }
}
