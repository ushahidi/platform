<?php defined('SYSPATH') or die('No direct access');
/**
 * Kohana exception class. Translates exceptions using the [I18n] class.
 *
 * @package    Kohana
 * @category   Exceptions
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Kohana_Exception extends Exception {

	/**
	 * @var  array  PHP error code => human readable name
	 */
	public static $php_errors = array(
		E_ERROR              => 'Fatal Error',
		E_USER_ERROR         => 'User Error',
		E_PARSE              => 'Parse Error',
		E_WARNING            => 'Warning',
		E_USER_WARNING       => 'User Warning',
		E_STRICT             => 'Strict',
		E_NOTICE             => 'Notice',
		E_RECOVERABLE_ERROR  => 'Recoverable Error',
	);

	/**
	 * @var  string  error rendering view
	 */
	public static $error_view = 'kohana/error';

	/**
	 * @var  string  error view content type
	 */
	public static $error_view_content_type = 'text/html';

	/**
	 * Creates a new translated exception.
	 *
	 *     throw new Kohana_Exception('Something went terrible wrong, :user',
	 *         array(':user' => $user));
	 *
	 * @param   string          $message    error message
	 * @param   array           $variables  translation variables
	 * @param   integer|string  $code       the exception code
	 * @return  void
	 */
	public function __construct($message, array $variables = NULL, $code = 0)
	{
		if (defined('E_DEPRECATED'))
		{
			// E_DEPRECATED only exists in PHP >= 5.3.0
			Kohana_Exception::$php_errors[E_DEPRECATED] = 'Deprecated';
		}

		// Set the message
		$message = __($message, $variables);

		// Pass the message and integer code to the parent
		parent::__construct($message, (int) $code);

		// Save the unmodified code
		// @link http://bugs.php.net/39615
		$this->code = $code;
	}

	/**
	 * Magic object-to-string method.
	 *
	 *     echo $exception;
	 *
	 * @uses    Kohana_Exception::text
	 * @return  string
	 */
	public function __toString()
	{
		return Kohana_Exception::text($this);
	}

	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * @uses    Kohana_Exception::text
	 * @param   Exception   $e
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		try
		{
			// Get the exception information
			$type    = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();

			// Get the exception backtrace
			$trace = $e->getTrace();

			if ($e instanceof ErrorException)
			{
				if (isset(Kohana_Exception::$php_errors[$code]))
				{
					// Use the human-readable error name
					$code = Kohana_Exception::$php_errors[$code];
				}

				if (version_compare(PHP_VERSION, '5.3', '<'))
				{
					// Workaround for a bug in ErrorException::getTrace() that
					// exists in all PHP 5.2 versions.
					// @link http://bugs.php.net/45895
					for ($i = count($trace) - 1; $i > 0; --$i)
					{
						if (isset($trace[$i - 1]['args']))
						{
							// Re-position the args
							$trace[$i]['args'] = $trace[$i - 1]['args'];

							// Remove the args
							unset($trace[$i - 1]['args']);
						}
					}
				}
			}

			// Create a text version of the exception
			$error = Kohana_Exception::text($e);

			if (is_object(Kohana::$log))
			{
				// Add this exception to the log
				Kohana::$log->add(Log::ERROR, $error);

				$strace = Kohana_Exception::text($e)."\n--\n" . $e->getTraceAsString();
				Kohana::$log->add(Log::STRACE, $strace);

				// Make sure the logs are written
				Kohana::$log->write();
			}

			if (Kohana::$is_cli)
			{
				// Just display the text of the exception
				echo "\n{$error}\n";

				exit(1);
			}

			if ( ! headers_sent())
			{
				// Make sure the proper http header is sent
				$http_header_status = ($e instanceof HTTP_Exception) ? $code : 500;

				header('Content-Type: '.Kohana_Exception::$error_view_content_type.'; charset='.Kohana::$charset, TRUE, $http_header_status);
			}

			if (Request::$current !== NULL AND Request::current()->is_ajax() === TRUE)
			{
				// Just display the text of the exception
				echo "\n{$error}\n";

				exit(1);
			}

			// Start an output buffer
			ob_start();

			// Include the exception HTML
			if ($view_file = Kohana::find_file('views', Kohana_Exception::$error_view))
			{
				include $view_file;
			}
			else
			{
				throw new Kohana_Exception('Error view file does not exist: views/:file', array(
					':file' => Kohana_Exception::$error_view,
				));
			}

			// Display the contents of the output buffer
			echo ob_get_clean();

			exit(1);
		}
		catch (Exception $e)
		{
			// Clean the output buffer if one exists
			ob_get_level() and ob_clean();

			// Display the exception text
			echo Kohana_Exception::text($e), "\n";

			// Exit with an error status
			exit(1);
		}
	}

	/**
	 * Get a single line of text representing the exception:
	 *
	 * Error [ Code ]: Message ~ File [ Line ]
	 *
	 * @param   Exception   $e
	 * @return  string
	 */
	public static function text(Exception $e)
	{
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $e->getCode(), strip_tags($e->getMessage()), Debug::path($e->getFile()), $e->getLine());
	}

} // End Kohana_Exception
