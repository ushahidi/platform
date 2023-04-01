<?php

namespace Ushahidi\Modules\V5\Repository\Survey;

use Ushahidi\Modules\V5\DTO\SurveyStatesSearchFields;

interface SurveyStatesRepository
{
    public function getRecipients($survey_id, SurveyStatesSearchFields $search_fields);
    public function countPendingMessages($survey_id);
    public function getSurveyType($survey_id);

    public function countOutgoingMessages($survey_id, SurveyStatesSearchFields $search_fields);
    public function getPostCountByDataSource($survey_id, SurveyStatesSearchFields $search_fields);
    public function getResponseRecipients($survey_id, SurveyStatesSearchFields $search_fields);

    public function countTotalPending($survey_id, $total_sent);

    public function getResponses($survey_id, SurveyStatesSearchFields $search_fields);
}
