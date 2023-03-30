<?php

namespace Ushahidi\Modules\V5\Repository\Survey;

use Ushahidi\Modules\V5\Models\SurveyRole;
use Ushahidi\Core\Entity\FormRole as SurveyRoleEntity;
use Illuminate\Support\Facades\DB;

class EloquentSurveyRoleRepository implements SurveyRoleRepository
{
    /**
     * This method will fetch all the SurveyRole for the logged task from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param string $sortBy
     * @param string $order
     * @param ?int $survey_id
     * @return SurveyRole[]
     */
    public function fetchBySurveyId(
        string $sortBy,
        string $order,
        int $survey_id
    ) {
        SurveyRole::select('role_id')
            ->where('survey_id', '=', $survey_id)
            ->orderBy($sortBy, $order)
            ->get();
    }


    public function create(SurveyRoleEntity $survey_role_entity): SurveyRole
    {
        DB::beginTransaction();
        try {
            $survey = SurveyRole::create($survey_role_entity->asArray());
            DB::commit();
            return $survey;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the SurveyRole
     * @param int $id
     * @param SurveyRoleEntity $survey_role_entity
     */

    public function deleteBySurveyId(int $survey_id): void
    {
        SurveyRole::where("form_id", "=", $survey_id)->delete();
    }
}
