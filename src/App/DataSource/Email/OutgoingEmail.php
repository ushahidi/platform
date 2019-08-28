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
use Ushahidi\App\Multisite\UsesSiteInfo;
use Illuminate\Contracts\Mail\Mailer;
use Ushahidi\Core\Entity\Contact;
use Log;

class OutgoingEmail implements OutgoingAPIDataSource
{
    use MapsInboundFields, UsesSiteInfo;

    protected $config;
    protected $mailer;
    protected $messageRepo;

    /**
     * Constructor function for DataSource
     */
    public function __construct(
        array $config,
        Mailer $mailer = null
    ) {
        $this->config = $config;
        $this->mailer = $mailer;
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
        $site_name = $this->getSite()->getName();
        $site_email = $this->getSite()->getEmail();

        try {
            $this->mailer->send(
                'emails/outgoing-message',
                [
                    'message_text' => $message,
                    'site_url' => $this->getSite()->getClientUri(),
                ],
                function ($message) use ($to, $title, $site_email, $site_name) {
                    $message->to($to);
                    $message->subject($title);
                    if ($site_email) {
                        $message->from($site_email, $site_name);
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
