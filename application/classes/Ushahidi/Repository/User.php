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

class Ushahidi_Repository_User implements
	UserRepository,
	RegisterRepository
{
	// UserRepository
	public function get($id)
	{
		$query = DB::select('*')
			->from('users')
			->where('id', '=', $id)
			;
		$result = $query->execute();
		return new User($result->current());
	}

	// UserRepository
	public function getByUsername($username)
	{
		$query = DB::select('*')
			->from('users')
			->where('username', '=', $username)
			;
		$result = $query->execute();
		return new User($result->current());
	}

	// UserRepository
	public function getByEmail($email)
	{
		$query = DB::select('*')
			->from('users')
			->where('email', '=', $email)
			;
		$result = $query->execute();
		return new User($result->current());
	}

	// RegisterRepository
	public function isUniqueUsername($username)
	{
		$query = DB::select('id')
			->from('users')
			->where('username', '=', $username)
			;
		$results = $query->execute();
		return (count($results) === 0);
	}

	// RegisterRepository
	public function isUniqueEmail($email)
	{
		$query = DB::select('id')
			->from('users')
			->where('email', '=', $email)
			;
		$results = $query->execute();
		return (count($results) === 0);
	}

	// RegisterRepository
	public function register($email, $username, $password)
	{
		$data = compact('email', 'username', 'password');
		$query = DB::insert('users')
			->columns(array_keys($data))
			->values(array_values($data))
			;
		list($userid) = $query->execute();
		return $userid;
	}
}
