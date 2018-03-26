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

// use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
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
        // ... pass the search information to the repo
        $this->repo->setSearchParams($search);

        $results = array('total_recipients' => 0, 'total_responses' => 0);
        // ... get the results of the search
        $results['total_recipients'] = $this->repo->getRecipients();
        $results['total_responses'] = $this->repo->getResponses();

        // ... and return the formatted results.
        return $this->formatter->__invoke($results);
    }
}
