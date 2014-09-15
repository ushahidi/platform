<?php

/**
 * Repository for Posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface PostRepository
{

	/**
	 * @param  int $id
	 * @param  int $parent_id
	 * @return \Ushahidi\Entity\Post
	 */
	public function get($id, $parent_id = null);

	/**
	 * @param  string $locale
	 * @param  int $parent_id
	 * @return \Ushahidi\Entity\Post
	 */
	public function getByLocale($locale, $parent_id);

	/**
	 * Set the conditions and parameters for a search.
	 *
	 * TODO: this can be generic and use SearchData::getSortingParams() instead
	 * of a separate $params argument.
	 *
	 * @param  Ushahidi\Entity\PostSearchData $data
	 * @param  Array $params [limit, offset, orderby, order, type]
	 * @return [Ushahidi\Entity\Post, ...]
	 */
	public function setSearchParams(PostSearchData $data, Array $params = null);

	/**
	 * Get the results for the current search parameters.
	 * @return [Ushahidi\Entity\Post, ...]
	 */
	public function getSearchResults();

	/**
	 * Get the number of possible records for the current search parameters.
	 *
	 * NOTE: that this may not always match up with the number of records fetched
	 * from a search, due to records being removed from the results because of
	 * access controls.
	 *
	 * @return Integer
	 */
	public function getSearchTotal();
}
