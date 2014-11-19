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

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Usecase\User\RegisterRepository;
use Ushahidi\Core\Data;
use Ushahidi\Core\Tool\Hasher;

class Ushahidi_Repository_User extends Ushahidi_Repository implements
	UserRepository,
	RegisterRepository
{

	protected $hasher;

	public function setHasher(Hasher $hasher)
	{
		$this->hasher = $hasher;
		return $this;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'users';
	}

	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if (!empty($search->q))
		{
			$query->and_where_open();
			$query->where('email', 'LIKE', "%" . $search->q . "%");
			$query->or_where('username', 'LIKE', "%" . $search->q . "%");
			$query->or_where('realname', 'LIKE', "%" . $search->q . "%");
			$query->and_where_close();
		}

		if (!empty($search->email))
		{
			$query->where('email', '=', $search->email);
		}
		
		if (!empty($search->realname))
		{
			$query->where('realname', '=',$search->realname);
		}

		if (!empty($search->username))
		{
			$query->where('username', '=', $search->username);
		}

		if (! empty($search->role))
		{
			$query->where('role', '=', $search->role);
		}
		
		return $query;
	}

	// CreateRepository
	public function create(Data $input)
	{
		$data = $input->asArray();

		$data['created'] = time();

		if (!isset($data['role'])) 
		{ 
			$data['role'] = 'user';
		}

		$data['password'] = $this->hasher->hash($data['password']);

		return $this->executeInsert($data);
	}

	// CreateRepository
	public function update($id, Data $input)
	{
		$data = $input->asArray();

		$data['updated'] = time();

		if (isset($data['password']) and !empty($data['password']))
		{
			$data['password'] = $this->hasher->hash($data['password']);
		}

		return $this->executeUpdate(compact('id'), $data);
	}


	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new User($data);
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
		return $this->executeInsert(compact('email', 'username', 'password', 'created'));
	}
}
