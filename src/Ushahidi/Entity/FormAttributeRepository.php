<?php

/**
 * Repository for Form Attributes
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface FormAttributeRepository
{
	/**
	 * @param  int $id
	 * @return Ushahidi\Entity\FormAttribute
	 */
	public function get($id);

	/**
	 * @return [Ushahidi\Entity\FormAttribute, ...]
	 */
	public function getAll();

}
