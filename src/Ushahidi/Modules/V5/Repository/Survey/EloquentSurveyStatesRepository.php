<?php

namespace Ushahidi\Modules\V5\Repository\Survey;

use Ushahidi\Modules\V5\Models\Survey;
use Illuminate\Pagination\LengthAwarePaginator;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\DTO\SurveyStatesSearchFields;
use Ushahidi\Modules\V5\Models\SurveyRole;
use DB;

class EloquentSurveyStatesRepository implements SurveyStatesRepository
{
    private function betweenDates($query, $column, $before_dt = null, $after_dt = null)
    {
        if ($before_dt && $after_dt) {
            $query->whereBetween($column, [strtotime($after_dt), strtotime($before_dt)]);
        } elseif ($before_dt) {
            $query->where($column, '<=', strtotime($before_dt));
        } elseif ($after_dt) {
            $query->where($column, '>=', strtotime($after_dt));
        }
        return $query;
    }

    public function getRecipients($survey_id, SurveyStatesSearchFields $search_fields)
    {
        $query = DB::table('contacts')->where(
            [
                ['posts.form_id', '=', $survey_id],
                [
                    'targeted_survey_state.survey_status',
                    'in',
                    [
                        Entity\TargetedSurveyState::RECEIVED_RESPONSE,
                        Entity\TargetedSurveyState::PENDING_RESPONSE,
                        Entity\TargetedSurveyState::SURVEY_FINISHED,
                    ]
                ],


            ]
        );

        $query->selectRaw('COUNT(contacts.id) as total');

        $query = $this->targetedSurveyStateJoin($query);
        if ($search_fields->createdAfter() || $search_fields->createdBefore()) {
            $query
                ->join('messages', 'messages.contact_id', '=', 'contacts.id')
                ->where('messages.direction', '=', 'outgoing');
            $query = $this->betweenDates(
                $query,
                'messages.created',
                $search_fields->createdBefore(),
                $search_fields->createdAfter()
            );
        }

        return $query->first()->total;
    }
    private function targetedSurveyStateJoin($query)
    {
        return $query->join('targeted_survey_state', 'contacts.id', '=', 'targeted_survey_state.contact_id')
            ->join('posts', 'posts.id', '=', 'targeted_survey_state.post_id');
    }
    public function countPendingMessages($survey_id)
    {
        $where = [
            'posts.form_id' => $form_id,
            'messages.direction' => 'outgoing',
            'messages.status' => 'pending',
            'targeted_survey_state.survey_status' => [
                Entity\TargetedSurveyState::RECEIVED_RESPONSE,
                Entity\TargetedSurveyState::PENDING_RESPONSE,
                Entity\TargetedSurveyState::SURVEY_FINISHED,
            ]
        ];
        $query = $this->selectQuery($where)
            ->resetSelect()
            ->select([DB::expr('COUNT(distinct message_id)'), 'total']);
        $query = $this->targetedSurveyStateJoin($query)
            ->join('messages', 'INNER')->on('messages.id', '=', 'targeted_survey_state.message_id');
        return $query
            ->execute($this->db())
            ->get('total');
    }
    public function getSurveyType($survey_id)
    {
    }
    public function countOutgoingMessages($survey_id, SurveyStatesSearchFields $search_fields)
    {
        $query = DB::table('messages')
            ->selectRaw('count(messages.status) as total, messages.status')
            ->where(
                DB::raw('post_id in (select post_id FROM targeted_survey_state WHERE form_id =' . $survey_id . ')')
            )
            ->where('direction', '=', 'outgoing')
            ->groupBy('status');

        $query = $this->betweenDates(
            $query,
            'messages.created',
            $search_fields->createdAfter(),
            $search_fields->createdBefore()
        );

        $result = $query->get();
        $ret = ['pending' => 0, 'sent' => 0];
        foreach ($result as $item) {
            if ($item - status === 'pending') {
                $ret['pending'] = $item->total;
            } elseif ($item->status === 'sent') {
                $ret['sent'] = $item->total;
            }
        }
        return $ret;
    }
    public function getPostCountByDataSource($survey_id, SurveyStatesSearchFields $search_fields)
    {
        $dataSourceCounts = $this->queryByDataSource(
            $survey_id,
            $search_fields->createdAfter(),
            $search_fields->createdBefore()
        );
        $result = [
            'sms' => $dataSourceCounts['sms'],
            'email' => $dataSourceCounts['email'],
            'twitter' => $dataSourceCounts['twitter'],
            'web' => $this->queryForWeb(
                $survey_id,
                $search_fields->createdAfter(),
                $search_fields->createdBefore()
            ),
        ];
        $result['all'] = $result['web'] + $result['email'] + $result['twitter'] + $result['sms'];
        return $result;
    }

    private function queryByDataSource($survey_id, $created_after, $created_before)
    {
        $query = DB::table('posts')
            ->selectRaw('messages.type,COUNT(messages.id) as total')
            ->join('messages', 'posts.id', 'messages.post_id')
            ->where('posts.form_id', '=', $survey_id);
        $query = $this->betweenDates($query, 'posts.created', $created_before, $created_after);

        $result = $query->groupBy('messages.type')->get();
        $ret = ['sms' => 0, 'email' => 0, 'twitter' => 0];
        foreach ($result as $item) {
            if ($item->type === 'sms') {
                $ret['sms'] = $item->total;
            } elseif ($item->type === 'email') {
                $ret['email'] = $item->total;
            } elseif ($item->type === 'twitter') {
                $ret['twitter'] = $item->total;
            }
        }
        return $ret;
    }


    private function queryForWeb($survey_id, $created_after, $created_before)
    {
        $query = DB::table('posts')
            ->select('messages.id')
            ->leftJoin('messages', 'posts.id', '=', 'messages.post_id')
            ->where('posts.form_id', '=', $survey_id)
            ->whereNull('messages.post_id')
            ->where('posts.type', '=', 'report');

        $query = $this->betweenDates($query, 'posts.created', $created_before, $created_after);
        return $query->count();
    }

    public function getResponseRecipients($survey_id, SurveyStatesSearchFields $search_fields)
    {
        $query = DB::table('posts')
            ->selectRaw('COUNT(messages.contact_id) as total')
            ->distinct()
            ->join('messages', 'messages.post_id', '=', 'posts.id')
            ->where('form_id', '=', $survey_id)
            ->where('messages.direction', '=', 'incoming');

        $query = $this->betweenDates(
            $query,
            'posts.created',
            $search_fields->createdAfter(),
            $search_fields->createdBefore()
        );
        return $query->first()->total;
    }
    public function countTotalPending($survey_id, $total_sent)
    {
        $survey_id = intval($survey_id);
        $total_contacts = $this->getTotalContacts($survey_id);
        $total_attributes = $this->getTotalAttributes($survey_id);
        $total_pending_for_inactive = $this->getPendingCountQuery($survey_id);

        //  $total_pending_for_inactive = DB::query(Database::SELECT, $this->getPendingCountQuery())
        //    ->bind(':form_id', $form_id)
        //   ->execute($this->db())->get('total');
        return ($total_contacts * $total_attributes) - $total_sent - $total_pending_for_inactive;
    }

    private function getTotalContacts($survey_id)
    {
        $query = DB::table('targeted_survey_state')
            ->selectRaw('count(contact_id) as total')
            ->where("form_id", '=', $survey_id)
            ->where("survey_status", '<>', 'SURVEY FINISHED');
        return $query->first()->total;
    }

    private function getTotalAttributes($survey_id)
    {
        $query = DB::table('form_attributes')
            ->selectRaw('count(form_attributes.id) as total')
            ->join('form_stages', 'form_attributes.form_stage_id', '=', 'form_stages.id')
            ->where("form_stages.form_id", '=', $survey_id);
        return $query->first()->total;
    }

    private function getPendingCountQuery($survey_id)
    {
        /**
         * Selects attribute priority & id by contact,for contacts marked in targeted_survey_state as Inactive
         * (noted by the ACTIVE CONTACT IN SURVEY  # format of survey_status)
         */
        $attributeListQuery =
            "SELECT
				form_attributes.priority,
				targeted_survey_state.form_attribute_id,
				targeted_survey_state.contact_id
			FROM form_attributes
			INNER JOIN targeted_survey_state
				ON form_attributes.id =targeted_survey_state.form_attribute_id
			WHERE targeted_survey_state.form_id=" . $survey_id . "
				and targeted_survey_state.survey_status LIKE 'ACTIVE CONTACT IN SURVEY%'";
        /**
         * counts attributes that have a priority higher than the one of the attributess referenced
         * in targeted_survey_state for each invalidated contact
         */
        $attributeCountQuery = "
			SELECT count(form_attributes.form_stage_id) as counted from targeted_survey_state
			INNER JOIN form_stages ON targeted_survey_state.form_id=form_stages.form_id
			INNER JOIN form_attributes ON form_stages.id = form_attributes.form_stage_id
			INNER JOIN ($attributeListQuery) as internal_query
			ON internal_query.contact_id = targeted_survey_state.contact_id
			WHERE form_attributes.priority > internal_query.priority
			AND survey_status LIKE 'ACTIVE CONTACT IN SURVEY%' AND form_stages.form_id=" . $survey_id . "
			GROUP BY targeted_survey_state.contact_id, form_attributes.form_stage_id";
        /**
         * sums the result of the previous joined queries to gget how many attributes where not yet sent
         * for invalidated contacts
         */
        $sql = "SELECT SUM(results.counted) as total FROM ($attributeCountQuery) as results";
        $results = DB::select($sql);
        return $results[0]->total;
    }


    public function getResponses($survey_id, SurveyStatesSearchFields $search_fields)
    {
        $query = DB::table('contacts')->where(
            [
                ['posts.form_id', '=', $survey_id],
                ['messages.direction', '=', 'incoming'],
                [
                    'targeted_survey_state.survey_status',
                    'in',
                    [
                        Entity\TargetedSurveyState::RECEIVED_RESPONSE,
                        Entity\TargetedSurveyState::PENDING_RESPONSE,
                        Entity\TargetedSurveyState::SURVEY_FINISHED,
                    ]
                ],


            ]
        );

        $query = $this->betweenDates(
            $query,
            'posts.created',
            $search_fields->createdAfter(),
            $search_fields->createdBefore()
        );

        $query->selectRaw('COUNT(messages.id) as total');

        $query->join('targeted_survey_state', 'contacts.id', '=', 'targeted_survey_state.contact_id')
            ->join('posts', 'posts.id', '=', 'targeted_survey_state.post_id')
            ->join('messages', 'messages.post_id', '=', 'targeted_survey_state.post_id');
        return $query->first()->total;
    }
}
