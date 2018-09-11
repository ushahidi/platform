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

class LumenMailer implements MailerContract
{
    public function __construct(Mailer $mailer, $siteConfig, $clientUrl)
    {
        $this->mailer = $mailer;
        $this->siteConfig = $siteConfig;
        $this->clientUrl = $clientUrl;
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
        $site_name = $this->siteConfig['name'];
        $site_email = $this->siteConfig['email'];
        $multisite_email = config('multisite.email');

        // @todo make this more robust
        if ($multisite_email) {
            $from_email = $multisite_email;
        } elseif ($site_email) {
            $from_email = $site_email;
        } else {
            $from_email = false;
            // Get host from lumen
            // $host = app()->make('request')->getHost();
            // $from_email = 'noreply@' . $host;
        }

        $data = [
            'site_name' => $site_name,
            'token' => $params['token'],
            'client_url' => $this->clientUrl
        ];

        $subject = $site_name . ': Password reset';

        $this->mailer->send(
            'emails/forgot-password',
            $data,
            function ($message) use ($to, $subject, $from_email, $site_name) {
                $message->to($to);
                $message->subject($subject);
                if ($from_email) {
                    $message->from($from_email, $site_name);
                }
            }
        );
    }
}
