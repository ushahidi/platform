<?php

/**
 * Repository for Post Values
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

interface PostValueRepository
{
	/**
	 * @param  int $id
	 * @param  int $post_id
	 * @param  int $form_attribute_id
	 * @return Ushahidi\Core\Entity\PostValue
	 */
	public function get($id, $post_id = null, $form_attribute_id = null);

	/**
	 * Get a query to return matching values LIKE some value
	 *
	 * @param  int    $form_attribute_id
	 * @param  string $match
	 * @return Database_Query
	 */
	public function getValueQuery($form_attribute_id, array $matches);

	/**
	 * Get the table name for use in joins
	 * @return String
	 */
	public function getValueTable();
}
