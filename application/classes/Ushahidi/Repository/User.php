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

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Tool\Hasher;
use Ushahidi\Core\Usecase\User\RegisterRepository;

class Ushahidi_Repository_User extends Ushahidi_Repository implements
	UserRepository,
	RegisterRepository
{
	/**
	 * @var Hasher
	 */
	protected $hasher;

	/**
	 * @param  Hasher $hasher
	 * @return $this
	 */
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

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new User($data);
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$state = [
			'created'  => time(),
			'password' => $this->hasher->hash($entity->password),
		];

		return parent::create($entity->setState($state));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$state = [
			'updated'  => time(),
		];

		if ($entity->hasChanged('password')) {
			$state['password'] = $this->hasher->hash($entity->password);
		}

		return parent::update($entity->setState($state));
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['username', 'email', 'role', 'q' /* LIKE realname, username */];
	}

	// SearchRepository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->q)
		{
			$query->and_where_open();
			$query->where('email', 'LIKE', "%" . $search->q . "%");
			$query->or_where('username', 'LIKE', "%" . $search->q . "%");
			$query->or_where('realname', 'LIKE', "%" . $search->q . "%");
			$query->and_where_close();
		}

		if ($search->role) {
			$query->where('role', '=', $search->role);
		}

		return $query;
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
	public function register(Entity $entity)
	{
		return $this->executeInsert([
				'email'    => $entity->email,
				'username' => $entity->username,
				'password' => $this->hasher->hash($entity->password),
				'created'  => time()
			]);
	}
}
