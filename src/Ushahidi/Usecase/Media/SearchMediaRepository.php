<?php

/**
 * Ushahidi Search Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

interface SearchMediaRepository
{
	/**
	 * @param  Ushahidi\Entity\SearchMediaData $data
	 * @param  Array $params [limit, offset, orderby, order]
	 * @return $this
	 */
	public function setSearchParams(SearchMediaData $data, Array $params = null);

	/**
	 * @return [Ushahidi\Entity\Media, ...]
	 */
	public function getSearchResults();

	/**
	 * @return Integer
	 */
	public function getSearchTotal();
}
