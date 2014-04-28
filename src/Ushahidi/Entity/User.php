<?php

namespace Ushahidi\Entity;

use Ushahidi\Traits\ArrayExchange;

class User
{
	use ArrayExchange;

	public $id;
	public $email;
	public $realname;
	public $username;
	public $password;
	public $logins = 0;
	public $failed_attempts = 0;
	public $last_login;
	public $last_attempt;
	public $created;
	public $updated;
	public $role = 'user';
}
