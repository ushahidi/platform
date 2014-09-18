<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Repository
 *
 * Also implements registration checks
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\User;
use Ushahidi\Entity\UserRepository;
use Ushahidi\Usecase\User\RegisterRepository;

class Ushahidi_Repository_User extends Ushahidi_Repository implements
	UserRepository,
	RegisterRepository
{
	protected function getTable()
	{
		return 'users';
	}

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new User($data);
	}

	// UserRepository
	public function get($id)
	{
		return $this->getEntity($this->selectOne(compact('id')));
	}

	// UserRepository
	public function getByUsername($username)
	{
		return $this->getEntity($this->selectOne(compact('username')));
	}

	// UserRepository
	public function getByEmail($email)
	{
		return $this->getEntity($this->selectOne(compact('email')));
	}

	// UserRepository
	public function doesUserExist($id)
	{
		return $this->selectCount(compact('id')) !== 0;
	}

	// RegisterRepository
	public function isUniqueUsername($username)
	{
		return $this->selectCount(compact('username')) === 0;
	}

	// RegisterRepository
	public function isUniqueEmail($email)
	{
		return $this->selectCount(compact('email')) === 0;
	}

	// RegisterRepository
	public function register($email, $username, $password)
	{
		$created = time();
		return $this->insert(compact('email', 'username', 'password', 'created'));
	}
}
