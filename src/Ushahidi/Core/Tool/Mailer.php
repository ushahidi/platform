<?php

/**
 * Ushahidi Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Illuminate\Support\Str;
use Illuminate\Contracts\Mail\Mailer as LaravelMailer;
use Ushahidi\Contracts\Mailer as MailerContract;
use Ushahidi\Core\Concerns\UsesSiteInfo;

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

        $data = [
            'client_url' => $site_client_url,
            'site_name' => $site_name,
            'user_name' => $params['user_name'],
            'reset_string' => $params['string'],
            'reset_code' => $params['code'],
            'duration' => $params['duration']
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
