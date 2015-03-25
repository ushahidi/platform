<?php

/**
 * Ushahidi Platform Post Stats Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\SearchUsecase;

class StatsPost extends SearchUsecase
{
	// Usecase
	public function interact()
	{
		// Fetch an empty entity...
		$entity = $this->getEntity();

		// ... verify that the entity can be searched by the current user
		$this->verifySearchAuth($entity);

		// ... and get the search filters for this entity
		$search = $this->getSearch();

		// ... check if the user is allowed to published posts
		// ... if not filter to only published posts
		if (! $this->auth->isAllowed($entity, 'change_status')) {
			$search->status = 'published';
		}

		// ... get the total count for the search
		$results = $this->repo->getGroupedTotals($search);

		// ... pass the search information to the formatter, for paging
		$this->formatter->setSearch($search);

		// ... and return the formatted results.
		return $this->formatter->__invoke($results);
	}
}
