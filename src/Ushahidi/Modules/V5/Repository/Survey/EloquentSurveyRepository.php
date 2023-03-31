<?php

namespace Ushahidi\Modules\V5\Repository\Survey;

use Ushahidi\Modules\V5\Models\Survey;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository as SurveyRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Form as SurveyEntity;
use Ushahidi\Modules\V5\DTO\SurveySearchFields;
use Ushahidi\Modules\V5\Models\SurveyRole;

class EloquentSurveyRepository implements SurveyRepository
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
    ): LengthAwarePaginator {
        return $this->setSearchCondition(
            $search_fields,
            Survey::select($required_fields)
                ->take($limit)
                ->skip($skip)
                ->orderBy($sortBy, $order)
        )->paginate($limit ? $limit : config('paging.default_laravel_pageing_limit'));
    }

    /**
     * This method will fetch a single Survey from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @param array required_fields
     * @return Survey
     * @throws NotFoundException
     */
    public function findById(int $id, ?array $required_fields): Survey
    {

        $survey = $required_fields ?
            Survey::select($required_fields)->where('id', '=', $id)->first()
            : Survey::all()->where('id', '=', $id)->first();

        if (!$survey instanceof Survey) {
            throw new NotFoundException('Survey not found');
        }
        return $survey;
    }

    private function setSearchCondition(SurveySearchFields $survey_search_fields, $builder)
    {

        if ($survey_search_fields->q()) {
            $builder->where('name', 'LIKE', "%" . $survey_search_fields->q() . "%");
        }

        return $builder;
    }

    /**
     * This method will create a Survey
     * @param SurveyEntity $survey
     * @return Survey
     * @throws \Exception
     */
    public function create(SurveyEntity $survey_entity): Survey
    {
        DB::beginTransaction();
        try {
            $survey = Survey::create($survey_entity->asArray());
            DB::commit();
            return $survey;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Survey
     * @param int @id
     * @param SurveyEntity $survey_entity
     * @throws NotFoundException
     */
    public function update(int $id, SurveyEntity $survey_entity): void
    {
        $Survey = Survey::find($id);
        if (!$Survey instanceof Survey) {
            throw new NotFoundException('Survey not found');
        }

        DB::beginTransaction();
        try {
            Survey::find($id)->fill($survey_entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Survey
     * @param int $id
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id, ['id'])->delete();
    }

    /**
     * This method will return the roles that can create posts of a Survey
     * @param int $survey_id
     * @retrun SurveyRole[]
     */
    public function getRolesCanCreatePosts(int $survey_id)
    {
        return SurveyRole::where("form_id", "=", $survey_id)->with('role')->get();
    }
}
