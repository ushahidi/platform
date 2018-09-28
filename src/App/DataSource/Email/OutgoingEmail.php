<?php

namespace Ushahidi\App\DataSource\Email;

/**
 * Email Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Email
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\OutgoingAPIDataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\App\DataSource\Concerns\MapsInboundFields;
use Illuminate\Contracts\Mail\Mailer;
use Ushahidi\Core\Entity\Contact;
use Log;

class OutgoingEmail implements OutgoingAPIDataSource
{
    use MapsInboundFields;

    protected $config;
    protected $mailer;
    protected $messageRepo;

    /**
     * Constructor function for DataSource
     */
    public function __construct(
        array $config,
        Mailer $mailer = null,
        $siteConfig = null,
        $clientUrl = null
    ) {
        $this->config = $config;
        $this->mailer = $mailer;
        // @todo figure out a better way to set these. Maybe globally for all emails?
        $this->siteConfig = $siteConfig;
        $this->clientUrl = $clientUrl;
    }

    public function getName()
    {
        return 'OutgoingEmail';
    }

    public function getId()
    {
        return strtolower($this->getName());
    }

    public function getServices()
    {
        return [MessageType::EMAIL];
    }

    public function getOptions()
    {
        return [];
    }

    public function getInboundFields()
    {
        return [];
    }

    public function isUserConfigurable()
    {
        return false;
    }

    /**
     * Contact type user for this provider
     */
    public $contact_type = Contact::EMAIL;

    /**
     * @return mixed
     */
    public function send($to, $message, $title = "")
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

        try {
            $this->mailer->send(
                'emails/outgoing-message',
                [
                    'message_text' => $message,
                    'site_url' => $this->clientUrl
                ],
                function ($message) use ($to, $title, $from_email, $site_name) {
                    $message->to($to);
                    $message->subject($title);
                    if ($from_email) {
                        $message->from($from_email, $site_name);
                    }
                }
            );

            return [MessageStatus::SENT, false];
        } catch (\Exception $e) {
            Log::info("Couldn't send email:" . $e->getMessage());
            // Failed
            return [MessageStatus::FAILED, false];
        }
    }
}
