<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Mailer;
use Shadowhand\Email;
use League\Url\Url;

class Ushahidi_Mailer implements Mailer
{
	public function send($to, $type, Array $params = null)
	{
		$method = "send_".$type;
		if (method_exists($this, $method)) {
			$this->$method($to, $params);
		} else {
			// Exception
			throw new Exception('Unsupported mail type: ' + $type);
		}
	}

	protected function send_resetpassword($to, $params)
	{
		$site_name = Kohana::$config->load('site.name');
		$site_email = Kohana::$config->load('site.email');
		$multisite_email = Kohana::$config->load('multisite.email');
		$client_url = Kohana::$config->load('site.client_url');

		// @todo make this more robust
		if ($multisite_email) {
			$from_email = $multisite_email;
		} elseif ($site_email) {
			$from_email = $site_email;
		} else {
			$url = Url::createFromServer($_SERVER);
			$host = $url->getHost()->toUnicode();
			$from_email = 'noreply@' . $host;
		}

		$view = View::factory('email/forgot-password');
		$view->site_name = $site_name;
		$view->token = $params['token'];
		$view->client_url = $client_url;
		$message = $view->render();

		$subject = $site_name . ': Password reset';

		$email = Email::factory($subject, $message, 'text/html')
	        ->to($to)
	        ->from($from_email, $site_name)
	        ->send()
	        ;
	}
}
