# Friendly Error Pages

By default Kohana 3 doesn't have a method to display friendly error pages like that
seen in Kohana 2; In this short guide you will learn how it is done.

## Prerequisites

You will need `'errors' => TRUE` passed to `Kohana::init`. This will convert PHP
errors into exceptions which are easier to handle.

## 1. An Improved Exception Handler

Our custom exception handler is self explanatory.

	class Kohana_Exception extends Kohana_Kohana_Exception {

		public static function handler(Exception $e)
		{
			if (Kohana::$environment === Kohana::DEVELOPMENT)
			{
				parent::handler($e);
			}
			else
			{
				try
				{
					Kohana::$log->add(Log::ERROR, parent::text($e));

					$params = array
					(
						'action'  => 500,
						'message' => rawurlencode($e->getMessage())
					);

					if ($e instanceof HTTP_Exception)
					{
						$params['action'] = $e->getCode();
					}

					// Error sub-request.
					echo Request::factory(Route::get('error')->uri($params))
						->execute()
						->send_headers()
						->body();
				}
				catch (Exception $e)
				{
					// Clean the output buffer if one exists
					ob_get_level() and ob_clean();

					// Display the exception text
					echo parent::text($e);

					// Exit with an error status
					exit(1);
				}
			}
		}
	}

If we are in the development environment then pass it off to Kohana otherwise:

* Log the error
* Set the route action and message attributes.
* If a `HTTP_Exception` was thrown, then override the action with the error code.
* Fire off an internal sub-request.

The action will be used as the HTTP response code. By default this is: 500 (internal
server error) unless a `HTTP_Response_Exception` was thrown.

So this:

	throw new HTTP_Exception_404(':file does not exist', array(':file' => 'Gaia'));

would display a nice 404 error page, where:

	throw new Kohana_Exception('Directory :dir must be writable',
				array(':dir' => Debug::path(Kohana::$cache_dir)));

would display an error 500 page.

**The Route**

	Route::set('error', 'error/<action>(/<message>)', array('action' => '[0-9]++', 'message' => '.+'))
	->defaults(array(
		'controller' => 'error_handler'
	));

## 2. The Error Page Controller

	public function before()
	{
		parent::before();

		$this->template->page = URL::site(rawurldecode(Request::initial()->uri()));

		// Internal request only!
		if ( ! Request::current()->is_initial())
		{
			if ($message = rawurldecode($this->request->param('message')))
			{
				$this->template->message = $message;
			}
		}
		else
		{
			$this->request->action(404);
		}

		$this->response->status((int) $this->request->action());
	}

1. Set a template variable "page" so the user can see what they requested. This
   is for display purposes only.
2. If an internal request, then set a template variable "message" to be shown to
   the user.
3. Otherwise use the 404 action. Users could otherwise craft their own error messages, eg:
   `error/404/email%20your%20login%20information%20to%20hacker%40google.com`


		public function action_404()
		{
			$this->template->title = '404 Not Found';

			// Here we check to see if a 404 came from our website. This allows the
			// webmaster to find broken links and update them in a shorter amount of time.
			if (isset ($_SERVER['HTTP_REFERER']) AND strstr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) !== FALSE)
			{
				// Set a local flag so we can display different messages in our template.
				$this->template->local = TRUE;
			}

			// HTTP Status code.
			$this->response->status(404);
		}

		public function action_503()
		{
			$this->template->title = 'Maintenance Mode';
		}

		public function action_500()
		{
			$this->template->title = 'Internal Server Error';
		}

You will notice that each example method is named after the HTTP response code
and sets the request response code.

## 3. Conclusion

So that's it. Now displaying a nice error page is as easy as:

	throw new HTTP_Exception_503('The website is down');
