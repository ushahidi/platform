<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5QueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Survey\Queries\FetchSurveyStatsQuery;
use Ushahidi\Modules\V5\Repository\Survey\SurveyStatesRepository;
use Ushahidi\Modules\V5\Models\Survey;
use App\Bus\Query\QueryBus;

class FetchSurveyStatsQueryHandler extends V5QueryHandler
{

    private $survey_states_repository;
    private $queryBus;

    public function __construct(QueryBus $queryBus, SurveyStatesRepository $survey_states_repository)
    {
        $this->survey_states_repository = $survey_states_repository;
        $this->queryBus = $queryBus;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchSurveyStatsQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchSurveyStatsQuery $query
     * @return Survey
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);

        $total_responses = $this->survey_states_repository->getResponses(
            $query->getSurveyId(),
            $query->getSearchFields()
        );
        $total_recipients = $this->survey_states_repository->getRecipients(
            $query->getSurveyId(),
            $query->getSearchFields()
        );
        $total_response_recipients = $this->survey_states_repository->getResponseRecipients(
            $query->getSurveyId(),
            $query->getSearchFields()
        );
        $out_going_messages = $this->survey_states_repository->countOutgoingMessages(
            $query->getSurveyId(),
            $query->getSearchFields()
        );
        $total_messages_pending = $this->survey_states_repository->countTotalPending(
            $query->getSurveyId(),
            0
        );
        $total_by_data_source = $this->survey_states_repository->getPostCountByDataSource(
            $query->getSurveyId(),
            $query->getSearchFields()
        );

        $states = [
            "total_responses" => $total_responses,
            "total_recipients" => $total_recipients,
            "total_response_recipients" => $total_response_recipients,
            "total_messages_sent" => $out_going_messages['sent'],
            "total_messages_pending" => $total_messages_pending,
            "total_by_data_source" => $total_by_data_source
        ];
        return collect($states);
    }
}
