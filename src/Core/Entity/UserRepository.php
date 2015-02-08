<?php

/**
 * Repository for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

interface UserRepository
{
	/**
	 * @param  int $id
	 * @return \Ushahidi\Core\Entity\User
	 */
	public function get($id);

	/**
	 * @param string $username
	 * @return \Ushahidi\Core\Entity\User
	 */
	public function getByUsername($username);

	/**
	 * @param string $email
	 * @return \Ushahidi\Core\Entity\User
	 */
	public function getByEmail($email);

	/**
	 * @param int $id
	 * @return Boolean
	 */
	public function doesUserExist($id);
}
