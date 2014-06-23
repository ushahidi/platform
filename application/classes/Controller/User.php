<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_User extends Controller_Layout {

	public $template = 'user/main';

	protected $_auth;
	protected $_acl;
	protected $_user;

	protected $_redirect_whitelist = array(
		'oauth/authorize',
		'/'
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
		if (! $this->acl->allowed('login'))
		{
			$this->redirect('user' . URL::query());
		}

		if ($this->request->method() != 'POST')
		{
			$this->redirect('user' . URL::query());
		}

		$parser  = service('parser.user.login');
		$usecase = service('usecase.user.login');
		$params  = $this->request->post();

		try
		{
			$user = $parser($params);
			$userid = $usecase->interact($user);

			// TODO: move this into the use case, somehow, some way...
			$user = ORM::factory('User', $userid);
			$this->auth->complete_login($user);
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			$error = implode(', ', Arr::flatten($e->getErrors()));
		}
		catch (Ushahidi\Exception\AuthenticatorException $e)
		{
			$error = $e->getMessage();
		}

		if (empty($error)) {
			$to_url = $this->request->query('from_url');
			if (in_array(parse_url($to_url, PHP_URL_PATH), $this->_redirect_whitelist))
			{
				$this->redirect($to_url);
			}
			$this->redirect('user' . URL::query());
		}

		$this->template = View::factory('user/login')
			->set('error', $error)
			->set('form', $params);
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

		$parser  = service('parser.user.register');
		$usecase = service('usecase.user.register');
		$params  = $this->request->post();

		try
		{
			$user = $parser($params);
			$userid = $usecase->interact($user);

			return $this->action_submit_login();
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			$this->template = View::factory('user/register')
				->set('error', implode(', ', Arr::flatten($e->getErrors())))
				->set('form', $params);
		}
	}

	public function action_oauth()
	{
		$code = $this->request->query('code');
		if (!$this->auth->logged_in() OR !$code)
		{
			$this->redirect('oauth' . URL::query());
		}

		$session = $this->auth->session();
		$params  = $session->get('oauth');

		$request = Request::factory('oauth/token')
			->method(Request::POST)
			->post(array(
				'grant_type'    => 'authorization_code',
				'code'          => $code,
				'client_id'     => $params['client_id'],
				'client_secret' => $params['client_details']['secret'],
				'redirect_uri'  => $params['redirect_uri'],
				));

		$response = $request->execute();
		$json = json_decode($response->body());

		if (empty($json->access_token))
		{
			throw HTTP_Exception::factory(500, ":error : :description", array(
				":error" => $json->error,
				":description" => $json->error_description
			));
		}

		// Store the auth code in a cookie for the JS app
		Cookie::set('authtoken', $json->access_token);
		if (!empty($json->refresh_token))
		{
			Cookie::set('authrefresh', $json->refresh_token);
		}

		// Flow is complete
		$session->delete('oauth');

		$this->redirect('/');
	}

	public function action_logout()
	{
		Cookie::delete('authtoken');
		Cookie::delete('authrefresh');
		$this->auth->logout();
		if ($from_url = $this->request->query('from_url')
				AND in_array(parse_url($from_url, PHP_URL_PATH), $this->_redirect_whitelist)
			)
		{
			$this->redirect($from_url);
		}
		else
		{
			$this->redirect('/');
		}
	}

}
