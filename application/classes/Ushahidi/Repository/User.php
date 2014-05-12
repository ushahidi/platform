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

class Ushahidi_Repository_User implements UserRepository, RegisterRepository
{
	public function get($id)
	{
		$query = DB::select('*')
			->from('users')
			->where('id', '=', $id)
			;
		$result = $query->execute();
		return new User($result->current());
	}

	public function getByUsername($username)
	{
		$query = DB::select('*')
			->from('users')
			->where('username', '=', $username)
			;
		$result = $query->execute();
		return new User($result->current());
	}

	public function getByEmail($email)
	{
		$query = DB::select('*')
			->from('users')
			->where('email', '=', $email)
			;
		$result = $query->execute();
		return new User($result->current());
	}

	public function add(User $user)
	{
		$data = array_filter($user->asArray());
		unset($data['id']); // always autoinc
		$query = DB::insert('users')
			->columns(array_keys($data))
			->values(array_values($data))
			;
		list($user->id, $count) = $query->execute();
		return (bool) $count;
	}

	public function remove(User $user)
	{
		if (!$user->id)
		{
			throw new Exception("User does not have an id");
		}

		$query = DB::delete('users')
			->where('id', '=', $user->id)
			;
		$count = $query->execute();
		return (bool) $count;
	}

	public function edit(User $user)
	{
		$data = array_filter($user->asArray());
		unset($data['id']); // never update id
		$query = DB::update('users')
			->set($data)
			->where('id', '=', $user->id)
			;
		$count = $query->execute();
		return true;
	}

	public function isUniqueUsername($username)
	{
		$query = DB::select('id')
			->from('users')
			->where('username', '=', $username)
			;
		$results = $query->execute();
		return (count($results) === 0);
	}

	public function isUniqueEmail($email)
	{
		$query = DB::select('id')
			->from('users')
			->where('email', '=', $email)
			;
		$results = $query->execute();
		return (count($results) === 0);
	}
}
