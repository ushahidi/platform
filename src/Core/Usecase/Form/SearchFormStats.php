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
		// ... pass the search information to the repo
		$this->repo->setSearchParams($search);
		$outgoing = $this->repo->countOutgoingMessages($this->getIdentifier('form_id'));
		$results = array(
			'total_recipients' => $this->repo->getRecipients($this->getIdentifier('form_id')),
			'total_responses' => $this->repo->getResponses($this->getIdentifier('form_id')),
			'total_messages_sent' => $outgoing['sent'],
			'total_messages_pending' => $this->repo->countTotalPending(
				$this->getIdentifier('form_id'),
				$outgoing['sent']
			)
		);
		$entity->setState($results);
		// ... and return the formatted results.
		return $this->formatter->__invoke($entity);
	}
}
