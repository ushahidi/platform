<?php

/**
 * Ushahidi Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace App\Tools;

use Illuminate\Support\Str;
use Ushahidi\Core\Concerns\UsesSiteInfo;
use Ushahidi\Contracts\Mailer as MailerContract;
use Illuminate\Contracts\Mail\Mailer as LaravelMailer;

class Mailer implements MailerContract
{
    use UsesSiteInfo;

    protected $mailer;

    public function __construct(LaravelMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($to, $type, array $params = null)
    {
        // Only available type right now is 'resetpassword'
        $method = 'send'.Str::ucfirst($type);
        if (method_exists($this, $method)) {
            $this->$method($to, $params);
        } else {
            // Exception
            throw new \Exception('Unsupported mail type: ' + $type);
        }
    }

    protected function sendResetpassword($to, $params)
    {
        $site_name = $this->getSite()->getName();
        $site_email = $this->getSite()->getEmail();
        $site_client_url = $this->getSite()->getClientUri() ?? env('DEFAULT_CLIENT_URL');
        $token = $params['token'];

        $data = [
            'site_name' => $site_name,
            'token' => $token,
            'client_url' => $site_client_url,
        ];

        $subject = $site_name.': Password reset';

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
