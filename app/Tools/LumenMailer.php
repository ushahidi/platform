<?php

/**
 * Ushahidi Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Tools;

use Ushahidi\Core\Tool\Mailer as MailerContract;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Str;
use Ushahidi\App\Multisite\UsesSiteInfo;

class LumenMailer implements MailerContract
{
    use UsesSiteInfo;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($to, $type, array $params = null)
    {
        // Only available type right now is 'resetpassword'
        $method = "send" . Str::ucfirst($type);
        if (method_exists($this, $method)) {
            $this->$method($to, $params);
        } else {
            // Exception
            throw new Exception('Unsupported mail type: ' + $type);
        }
    }

    protected function sendResetpassword($to, $params)
    {
        $site_name = $this->getSite()->getName();
        $site_email = $this->getSite()->getEmail();

        $data = [
            'site_name' => $site_name,
            'token' => $params['token'],
            'client_url' => $this->getSite()->getClientUri()
        ];

        $subject = $site_name . ': Password reset';

        $this->mailer->send(
            'emails/forgot-password',
            $data,
            function ($message) use ($to, $subject, $site_email, $site_name) {
                $message->to($to);
                $message->subject($subject);
                if ($site_email) {
                    $message->from($site_email, $site_name);
                }
            }
        );
    }
}
