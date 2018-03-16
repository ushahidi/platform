<?php

/**
 * Repository for Contacts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;

interface ContactPostStateRepository extends
	EntityGet,
	EntityExists
{

	/**
	 * @param string  $contact
	 * @param string  $type
	 * @return boolean
	 */
	public function getByContact($contact, $type);
	/**
	 * @param string  $contact
	 * @param string  $type
	 * @return boolean
	 */
	public function getByPost($post);
}
