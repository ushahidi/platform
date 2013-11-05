<?php defined('SYSPATH') OR die('No direct script access.');

class OAuth2_Storage_ORM extends Koauth_OAuth2_Storage_ORM {

	public function checkUserCredentials($username, $password)
	{
		$auth = A1::instance();
		
		$user = ORM::factory($this->config['user_model']);
		$user
			->where($user->unique_key($username), '=', $username)
			->find();
		return $auth->check_password($user, $password);
	}

}
