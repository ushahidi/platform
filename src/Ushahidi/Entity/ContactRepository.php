<?php

/**
 * Repository for Contacts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface ContactRepository
{

	/**
	 * @param  int $id
	 * @return \Ushahidi\Entity\Contact
	 */
	public function get($id);

	/**
	 * @param \Ushahidi\Entity\Contact
	 * @return boolean
	 */
	public function add(Contact $contact);

	/**
	 * @param \Ushahidi\Entity\Contact
	 * @return boolean
	 */
	public function remove(Contact $contact);

	/**
	 * @param \Ushahidi\Entity\Contact
	 * @return boolean
	 */
	public function edit(Contact $contact);

}

