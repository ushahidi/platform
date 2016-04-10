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
use Ushahidi\Core\Usecase\User\ResetPasswordRepository;

use PragmaRX\Google2FA\Google2FA;

class Ushahidi_Repository_User extends Ushahidi_Repository implements
	UserRepository,
	RegisterRepository,
	ResetPasswordRepository
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
		return ['email', 'role', 'q' /* LIKE realname, email */];
	}

	// SearchRepository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->q)
		{
			$query->and_where_open();
			$query->where('email', 'LIKE', "%" . $search->q . "%");
			$query->or_where('realname', 'LIKE', "%" . $search->q . "%");
			$query->and_where_close();
		}

		if ($search->role) {
			$query->where('role', '=', $search->role);
		}

		return $query;
	}

	// UserRepository
	public function getByEmail($email)
	{
		return $this->getEntity($this->selectOne(compact('email')));
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
			'realname' => $entity->realname,
			'email'    => $entity->email,
			'password' => $this->hasher->hash($entity->password),
			'created'  => time()
			]);
	}

	// ResetPasswordRepository
	public function getResetToken(Entity $entity) {
		// Todo: replace with something more robust.
		// This is predictable if we don't have the openssl mod
		$token = Security::token(TRUE);

		$input = [
			'reset_token' => $token,
			'user_id' => $entity->id,
			'created' => time()
		];

		// Save the token
		$query = DB::insert('user_reset_tokens')
			->columns(array_keys($input))
			->values(array_values($input))
			->execute($this->db);

		return $token;
	}

	// ResetPasswordRepository
	public function isValidResetToken($token) {
		$result = DB::select([DB::expr('COUNT(*)'), 'total'])
			->from('user_reset_tokens')
			->where('reset_token', '=', $token)
			->where('created', '>', time() - 1800) // Expire tokens after less than 30 mins
			->execute($this->db);

		$count = $result->get('total') ?: 0;

		return $count !== 0;
	}

	// ResetPasswordRepository
	public function setPassword($token, $password) {
		$sub = DB::select('user_id')
			->from('user_reset_tokens')
			->where('reset_token', '=', $token);

		$this->executeUpdate(['id' => $sub], [
			'password' => $this->hasher->hash($password)
		]);
	}

	// ResetPasswordRepository
	public function deleteResetToken($token) {
		$result = DB::delete('user_reset_tokens')
			->where('reset_token', '=', $token)
			->execute($this->db);
	}

  /**
   * Verify User Google 2fa secret
   * @param Entity User
   * @param string secret
   * @return bool
   */
  public function verifyGoogle2fa(Entity $entity, $secret) {

    $google2fa = new Google2FA();
    $valid = false;
      
    $result = DB::select('google2fa_secret')
      ->from('user_google2fa_secrets')
			->where('user_id', '=', $entity->id)
			->execute($this->db);

    $google2fa_secret = $result->get('google2fa_secret');
    $valid = $google2fa->verifyKey($google2fa_secret, $secret, 100);

    return $valid;
  }

  /**
   * Disable Google 2fa secret
   * @param Entity User
   */
  public function disableGoogle2fa(Entity $entity) {
    $result = DB::delete('user_google2fa_secrets')
			->where('user_id', '=', $entity->id)
			->execute($this->db);
  }
  /**
   * Set Google 2fa secret
   * @param Entity User
   * @return string Google QR url
   */
  public function generateGoogle2fa(Entity $entity) {
    $google2fa = new Google2FA();
    $google2fa_secret = $google2fa->generateSecretKey();
    $google2fa_url = $google2fa->getQRCodeGoogleUrl(
        'Ushahidi',
        $entity->email,
        $google2fa_secret
    );

    $input = [
			'google2fa_secret' => $google2fa_secret,
			'user_id' => $entity->id,
			'created' => time()
		];

		// Save the secret
    if (!$entity->google2fa_enabled)
    {
		  $query = DB::insert('user_google2fa_secrets')
			  ->columns(array_keys($input))
	  		->values(array_values($input))
		  	->execute($this->db);
    }
    else
    {
      $query = DB::update('user_google2fa_secrets')
			  ->set($input)
        ->where('user_id', '=', $entity->id)
		  	->execute($this->db);
    }

    return $google2fa_url;
  }

	/**
	 * Get total count of entities
	 * @param  Array $where
	 * @return int
	 */
	public function getTotalCount(Array $where = [])
	{
		return $this->selectCount($where);
	}
}
