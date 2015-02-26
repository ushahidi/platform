<?php

/**
 * Repository for Messages
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;

interface MessageRepository extends
	EntityGet
{
	/**
	 * @param \Ushahidi\Core\Entity\Message
	 * @return array of \Ushahidi\Core\Entity\Message
	 */
	public function getAllByParent(Message $parent);

	/**
	 * @param \Ushahidi\Core\Entity\Message
	 * @return boolean
	 */
	public function add(Message $contact);

	/**
	 * @param \Ushahidi\Core\Entity\Message
	 * @return boolean
	 */
	public function remove(Message $contact);

	/**
	 * @param \Ushahidi\Core\Entity\Message
	 * @return boolean
	 */
	public function edit(Message $contact);
}
