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
		$client_url = Kohana::$config->load('site.client_url');

		$site_email = $site_email ? $site_email : 'noreply@' . URL::base();

		$subject = $site_name . ': Password reset';
		$message =
'Hello,

A request has been made to reset your account password. To reset your password, you will need to submit this token in order to verify that the request was legitimate.

Your password reset token is ' . $params['token'] . "\n\n";

if ($client_url) {
	$message .= "Click on the URL below to enter the token and proceed with resetting your password.\n\n";
	$message .= $client_url . '/forgotpassword/confirm/'.urlencode($params['token']);
}

$message .= 'Thank you.';

		$email = Email::factory($subject, $message)
	        ->to($to)
	        ->from($site_email, $site_name)
	        ->send()
	        ;
	}
}
