<?php

namespace Ushahidi\Modules\V5\Repository\Survey;

use Ushahidi\Modules\V5\Models\Survey;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Core\Entity\Form as SurveyEntity;
use Ushahidi\Modules\V5\DTO\SurveySearchFields;
use Ushahidi\Modules\V5\Models\SurveyRole;

interface SurveyRepository
{
    /**
     * This method will fetch all the Survey for the logged survey from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param SurveySearchFields search_fields
     * @param array required_fields
     * @return Survey[]
     */
    public function fetch(
        int $limit,
        int $skip,
        string $sortBy,
        string $order,
        SurveySearchFields $search_fields,
        array $required_fields
    ): LengthAwarePaginator;

    /**
     * This method will fetch a single Survey from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @param array required_fields
     * @return Survey
     * @throws NotFoundException
     */
    public function findById(int $id, ?array $required_fields): Survey;

    /**
     * This method will create a Survey
     * @param SurveyEntity $survey_entity
     * @return Survey
     */
    public function create(SurveyEntity $survey_entity): Survey;

    /**
     * This method will update the Survey
     * @param int $id
     * @param SurveyEntity $survey_entity
     */
    public function update(int $id, SurveyEntity $survey_entity): void;

    /**
     * This method will delete the Survey
     * @param int $id
     */
    public function delete(int $id): void;


    /**
     * This method will return the roles that can create posts of a Survey
     * @param int $survey_id
     * @retrun SurveyRole[]
     */
    public function getRolesCanCreatePosts(int $survey_id);
}
