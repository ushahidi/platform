<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_User extends Controller_Layout {

	public $template = 'user/main';

	protected $_auth;
	protected $_acl;
	protected $_user;

	protected $_redirect_whitelist = array(
		'oauth/authorize'
	);

	public function before()
	{
		parent::before();

		$this->acl  = A2::instance();
		$this->auth = $this->acl->auth();
		$this->user = $this->acl->get_user();

		$this->header->set('logged_in', $this->auth->logged_in());
	}

	public function action_index()
	{
		if (! $this->acl->allowed('logout'))
		{
			$this->redirect('user/login' . URL::query());
		}

		$this->template = View::factory('user/main');
	}

	public function action_login()
	{
		if (! $this->acl->allowed('user/login') AND ! $this->acl->allowed('register'))
		{
			if ($from_url = $this->request->query('from_url')
					AND in_array(parse_url($from_url, PHP_URL_PATH), $this->_redirect_whitelist)
				)
			{
				$this->redirect($from_url);
			}
			else
			{
				$this->redirect('user' . URL::query());
			}
		}

		$this->template = View::factory('user/login');
	}

	public function action_register()
	{
		if (! $this->acl->allowed('login') AND ! $this->acl->allowed('register'))
		{
			if ($from_url = $this->request->query('from_url')
					AND in_array(parse_url($from_url, PHP_URL_PATH), $this->_redirect_whitelist)
				)
			{
				$this->redirect($from_url);
			}
			else
			{
				$this->redirect('user' . URL::query());
			}
		}

		$this->template = View::factory('user/register');
	}

	public function action_submit_login()
	{
		try
		{
			if (! $this->acl->allowed('login'))
			{
				$this->redirect('user' . URL::query());
			}

			if ($this->request->method() != 'POST')
			{
				$this->redirect('user' . URL::query());
			}

			$params = $this->request->post();
			$valid = new Validation($params);
			$valid
				->rule('username', 'not_empty')
				->rule('password', 'not_empty')
				->rules('csrf', array(
					array('not_empty'),
					array('Security::check'),
					));

			if ($valid->check())
			{
				$user = $this->auth->login($params['username'], $params['password']);
				if ($user instanceof Model_User)
				{
					if ($from_url = $this->request->query('from_url')
							AND in_array(parse_url($from_url, PHP_URL_PATH), $this->_redirect_whitelist)
						)
					{
						$this->redirect($from_url);
					}
					else
					{
						$this->redirect('user' . URL::query());
					}
					return;
				}
				else
				{
					throw new Exception_Login('Log in failed - incorrect username or password');
				}
			}

			throw new Exception_Login('Log in failed - incorrect username or password');
		}
		catch (Exception_Login $e)
		{
			$this->template = View::factory('user/login')
				->set('error', $e->getMessage())
				->set('form', $params);
			return;
		}
		catch (A1_Rate_Exception $e)
		{
			$this->template = View::factory('user/login')
				->set('error', $e->getMessage())
				->set('form', $params);
			return;
		}

		// If we somehow fall through to here;
		$this->redirect('user');
	}

	public function action_submit_register()
	{
		if (! $this->acl->allowed('register'))
		{
			$this->redirect('user' . URL::query());
		}

		if ($this->request->method() != 'POST')
		{
			$this->redirect('user/login' . URL::query());
		}

		$params = $this->request->post();

		$user = ORM::factory('User')
			->values($params, array('username', 'password', 'email'));

		$valid = Validation::factory($params)
			->rules('csrf', array(
					array('not_empty'),
					array('Security::check')
				))
			->rules('username', array(
					array('not_empty')
				))
			->rules('password', array(
					array('not_empty')
				));

		// do login magic
		try
		{
			if ($user->check($valid))
			{
				$user->save();

				$this->redirect('user/login' . URL::query());
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->template = View::factory('user/register')
				->set('error', implode(', ', Arr::flatten($e->errors('models'))))
				->set('form', $params);
		}
	}

	public function action_logout()
	{
		if (! $this->acl->allowed('logout'))
		{
			$this->redirect('user/login' . URL::query());
		}

		$this->auth->logout();
		$this->redirect('user/login' . URL::query());
	}

}