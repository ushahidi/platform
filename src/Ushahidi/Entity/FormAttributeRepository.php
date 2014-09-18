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
	 * @param  string $key
	 * @param  int    $form_id
	 * @return Ushahidi\Entity\FormAttribute
	 */
	public function getByKey($key, $form_id = null);

	/**
	 * @return [Ushahidi\Entity\FormAttribute, ...]
	 */
	public function getAll();

	/**
	 * @param  int $form_id
	 * @return [Ushahidi\Entity\FormAttribute, ...]
	 */
	public function getRequired($form_id);

}
