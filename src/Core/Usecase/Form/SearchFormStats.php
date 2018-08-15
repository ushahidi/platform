<?php

/**
 * Ushahidi Platform Search Form Stats Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Entity\FormStats;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\SearchUsecase;

class SearchFormStats extends SearchUsecase
{
    use IdentifyRecords;
    /**
     * Get filter parameters and default values that are used for paging.
     *
     * @return Array
     */

    // Usecase
    public function interact()
    {
        // Fetch an empty entity...
        $entity = $this->getEntity();

        // ... verify that the entity can be searched by the current user
        $this->verifySearchAuth($entity);

        // ... and get the search filters for this entity
        $search = $this->getSearch();
        $search->setFilter('form_id', $this->getIdentifier('form_id'));
        $search->setFilter('created_after', $this->getIdentifier('created_after'));
        $search->setFilter('created_before', $this->getIdentifier('created_before'));

        // ... pass the search information to the repo
        $this->repo->setSearchParams($search);

        // Check the survey type so we can determine which stats to get
        $surveyType =  $this->repo->getSurveyType($this->getIdentifier('form_id'));

        // If we're dealing with a targeted survey, go get those states
        if ($surveyType[0]['targeted_survey']) {
            $results = $this->getTargetedSurveyStats();
        } else {
            $results = $this->getCrowdsourcedSurveyStats();
        }

        $entity->setState($results);
        // ... and return the formatted results.
        return $this->formatter->__invoke($entity);
    }

    private function getTargetedSurveyStats()
    {
        $outgoing = $this->repo->countOutgoingMessages(
        $this->getIdentifier('form_id'),
        $this->getIdentifier('created_after'),
        $this->getIdentifier('created_before')
        );
        $results = [
            'total_recipients' => $this->repo->getRecipients(
            $this->getIdentifier('form_id'),
            $this->getIdentifier('created_after'),
            $this->getIdentifier('created_before')
            ),
            'total_response_recipients' => $this->repo->getResponseRecipients(
            $this->getIdentifier('form_id'),
            $this->getIdentifier('created_after'),
            $this->getIdentifier('created_before')
            ),
            'total_responses' => $this->repo->getResponses(
            $this->getIdentifier('form_id'),
            $this->getIdentifier('created_after'),
            $this->getIdentifier('created_before')
            ),
            'total_messages_sent' => $outgoing['sent'],
            'total_messages_pending' => $this->repo->countTotalPending(
                $this->getIdentifier('form_id'),
                $outgoing['sent']
            )
        ];
        return $results;
    }

    private function getCrowdsourcedSurveyStats()
    {
        $results = [
            'total_by_data_source' => $this->repo->getPostCountByDataSource(
            $this->getIdentifier('form_id'),
            $this->getIdentifier('created_after'),
            $this->getIdentifier('created_before')
            )
        ];
        return $results;
    }
}
