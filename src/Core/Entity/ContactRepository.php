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

interface ContactRepository extends
	EntityGet
{
	/**
	 * @param \Ushahidi\Core\Entity\Contact
	 * @return boolean
	 */
	public function add(Contact $contact);

	/**
	 * @param \Ushahidi\Core\Entity\Contact
	 * @return boolean
	 */
	public function remove(Contact $contact);

	/**
	 * @param \Ushahidi\Core\Entity\Contact
	 * @return boolean
	 */
	public function edit(Contact $contact);
}
