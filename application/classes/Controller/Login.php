<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Login extends Controller_Template {
	
	public $template = 'login/main';
	
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
	}
	
	public function action_index()
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
				$this->redirect('login/done' . URL::query());
			}
		}

		$this->template = View::factory('login/main');
	}
	
	public function action_submit()
	{
		if (! $this->acl->allowed('login'))
		{
			$this->redirect('login/done' . URL::query());
		}
		
		if ($this->request->method() != 'POST')
		{
			$this->redirect('login' . URL::query());
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
					$this->redirect('login/done' . URL::query());
				}
				return;
			}
			else
			{
				throw new Kohana_Exception('Log in failed - incorrect username or password');
				$this->redirect('login' . URL::query());
				return;
			}
		}
		
		throw new Kohana_Exception('Log in failed - incorrect username or password');
		
	}
	
	public function action_register()
	{
		if (! $this->acl->allowed('register'))
		{
			$this->redirect('login/done' . URL::query());
		}
		
		if ($this->request->method() != 'POST')
		{
			$this->redirect('login' . URL::query());
		}
		
		$params = $this->request->post();

		$user = ORM::factory('User')
			->values($params, array('username', 'password'));
		
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
				
				$this->redirect('login' . URL::query());
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new Kohana_Exception('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models')))
				));
		}
	}
	
	public function action_done()
	{
		if (! $this->acl->allowed('logout'))
		{
			$this->redirect('login' . URL::query());
		}
		
		$this->template = View::factory('login/done');
	}
	
	public function action_logout()
	{
		if (! $this->acl->allowed('logout'))
		{
			$this->redirect('login' . URL::query());
		}
		
		$this->auth->logout();
		$this->redirect('login' . URL::query());
	}
	
}