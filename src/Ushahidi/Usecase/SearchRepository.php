<?php

/**
 * Ushahidi Search Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase;

use Ushahidi\SearchData;

interface SearchRepository
{
	/**
	 * Converts an array of entity data into an object.
	 * @param  Array $data
	 * @return Entity
	 */
	public function getEntity(Array $data = null);

	/**
	 * @param  SearchData $data
	 * @return $this
	 */
	public function setSearchParams(SearchData $data);

	/**
	 * @return [Ushahidi\Entity, ...]
	 */
	public function getSearchResults();

	/**
	 * @return Integer
	 */
	public function getSearchTotal();
}
