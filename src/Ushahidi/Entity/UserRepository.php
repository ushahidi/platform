<?php

/**
 * Repository for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface UserRepository
{
	/**
	 * @param  int $id
	 * @return \Ushahidi\Entity\User
	 */
	public function get($id);

	/**
	 * @param string $email
	 * @return \Ushahidi\Entity\User
	 */
	public function getByUsername($email);

	/**
	 * @param string $email
	 * @return \Ushahidi\Entity\User
	 */
	public function getByEmail($email);
}
