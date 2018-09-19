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

use Ushahidi\App\DataSource\IncomingAPIDataSource;
use Ushahidi\App\DataSource\OutgoingAPIDataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\App\DataSource\Concerns\MapsInboundFields;
use Ushahidi\Core\Entity\MessageRepository;
use Illuminate\Contracts\Mail\Mailer;
use Ushahidi\Core\Entity\Contact;
use Log;

class Email extends OutgoingEmail implements IncomingAPIDataSource
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
        $clientUrl = null,
        MessageRepository $messageRepo = null
    ) {
        $this->config = $config;
        $this->mailer = $mailer;
        // @todo figure out a better way to set these. Maybe globally for all emails?
        $this->siteConfig = $siteConfig;
        $this->clientUrl = $clientUrl;
        $this->messageRepo = $messageRepo;
    }

    public function getName()
    {
        return 'Email';
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
        return [
            'intro_text' => [
                'label' => '',
                'input' => 'read-only-text',
                'description' => 'In order to receive posts by email, please input your email account settings below'
            ],
            'incoming_type' => [
                'label' => 'Incoming Server Type',
                'input' => 'radio',
                'description' => '',
                'options' => ['POP', 'IMAP'],
                'rules' => ['required', 'number']
            ],
            'incoming_server' => [
                'label' => 'Incoming Server',
                'input' => 'text',
                'description' => '',
                'description' => 'Examples: mail.yourwebsite.com, imap.gmail.com, pop.gmail.com',
                'rules' => ['required']
            ],
            'incoming_port' => [
                'label' => 'Incoming Server Port',
                'input' => 'text',
                'description' => 'Common ports: 110 (POP3), 143 (IMAP), 995 (POP3 with SSL), 993 (IMAP with SSL)',
                'rules' => ['required','number']
            ],
            'incoming_security' => [
                'label' => 'Incoming Server Security',
                'input' => 'radio',
                'description' => '',
                'options' => ['None', 'SSL', 'TLS']
            ],
            'incoming_username' => [
                'label' => 'Incoming Username',
                'input' => 'text',
                'description' => '',
                'placeholder' => 'Email account username',
                'rules' => ['required']
            ],
            'incoming_password' => [
                'label' => 'Incoming Password',
                'input' => 'text',
                'description' => '',
                'placeholder' => 'Email account password',
                'rules' => ['required']
            ]
        ];
    }

    public function getInboundFields()
    {
        return [
            'Subject' => 'text',
            'Date' => 'datetime',
            'Message' => 'text'
        ];
    }

    public function isUserConfigurable()
    {
        return true;
    }

    /**
     * Contact type user for this provider
     */
    public $contact_type = Contact::EMAIL;

    /**
     * Fetch email messages from server
     *
     * For services where we have to poll for message (Twitter, Email, FrontlineSMS) this should
     * poll the service and pass messages to $this->receive()
     *
     * @param  boolean $limit   maximum number of messages to fetch at a time
     * @return int              number of messages fetched
     */
    public function fetch($limit = false)
    {
        // Return if no imap extension
        if (! function_exists('imap_open') && ! function_exists(__NAMESPACE__ . '\imap_open')) {
            app('log')->error("imap extension not enabled");
            return [];
        }

        $messages = [];

        $limit = 200;

        $type = $this->config['incoming_type'];
        $server = $this->config['incoming_server'];
        $port = $this->config['incoming_port'];
        $encryption = $this->config['incoming_security'];
        $username = $this->config['incoming_username'];
        $password = $this->config['incoming_password'];

        // Encryption type
        $encryption = (strcasecmp($encryption, 'none') != 0) ? '/'.$encryption : '';

        try {
            // Try to connect
            $inbox = '{'.$server.':'.$port.'/'.$type.$encryption.'}INBOX';
            $connection = @imap_open($inbox, $username, $password, 0, 1);

            $errors = imap_errors();
            $alerts = imap_alerts();

            // Return on connection error
            if (! $connection || $errors || $alerts) {
                $errors = is_array($errors) ? implode(', ', $errors) : "";
                $alerts = is_array($alerts) ? implode(', ', $errors) : "";
                Log::info("Could not connect to incoming email server", compact('errors', 'alerts'));
                return [];
            }

            $mailboxinfo = imap_check($connection);

            Log::info("Connected to $inbox", [$mailboxinfo]);

            $last_uid = $this->messageRepo->getLastUID('email');
            if ($last_uid > 0) {
                $max_range = $last_uid + $limit;
                $search_string = $last_uid ? $last_uid + 1 . ':' . $max_range : '1:' . $max_range;
                // Grab next set of messages by uid
                $emails = imap_fetch_overview($connection, $search_string, FT_UID);
                Log::info("Emails: ", [count($emails), $search_string]);
            } else {
                // Grab first set of messages by sequence numbers instead of uid
                // This avoids getting an empty set on the first fetch
                $max_range = $limit < $mailboxinfo->Nmsgs ? $limit : $mailboxinfo->Nmsgs;
                $search_string = "1:$max_range";
                $emails = imap_fetch_overview($connection, $search_string);
                Log::info("Emails: ", [count($emails), $search_string]);
            }

            if ($emails) {
                // reverse sort emails?
                //rsort($emails);
                foreach ($emails as $email) {
                    // Break out if we've hit our limit
                    // @todo revist and decide if this is worth doing when imap_search has grabbed everything anyway.
                    if ($limit and count($messages) >= $limit) {
                        break;
                    }

                    $message = $html_message = "";
                    $structure = imap_fetchstructure($connection, $email->uid, FT_UID);

                    // Get HTML message from multipart message
                    if (! empty($structure->parts)) {
                        $no_of_parts = count($structure->parts);

                        foreach ($structure->parts as $part_number => $part) {
                            if ($part->subtype == 'HTML') {
                                $html_message .= imap_fetchbody($connection, $email->uid, $part_number, FT_UID);
                            } elseif ($part->subtype == 'PLAIN') {
                                $message .= imap_fetchbody($connection, $email->uid, $part_number, FT_UID);
                            }
                        }
                    } else {
                        // or just fetch the body if not a multipart message
                        $message = imap_body($connection, $email->uid, FT_UID);
                    }

                    // Process the email
                    if (! empty($html_message)) {
                        $html_message = imap_qprint($html_message);
                        $messages[] = $this->processIncoming($email, $html_message);
                    } elseif (! empty($message)) {
                        $message = imap_qprint($message);
                        $messages[] = $this->processIncoming($email, $message);
                    }
                }
            }

            imap_errors();
            imap_alerts();

            imap_close($connection);
        } catch (\ErrorException $e) {
            $errors = imap_errors();
            $alerts = imap_alerts();
            $errors = is_array($errors) ? implode(', ', $errors) : "";
            $alerts = is_array($alerts) ? implode(', ', $errors) : "";
            Log::info($e->getMessage(), compact('errors', 'alerts'));
        }

        return $messages;
    }

    /**
     * Process individual incoming email
     *
     * @param object $overview
     * @param string message - the email message
     */
    protected function processIncoming($overview, $message)
    {
        $from = $this->getEmail($overview->from);
        $to = isset($overview->to) ? $this->getEmail($overview->to) : null;
        $title = isset($overview->subject) ? $overview->subject : null;
        $data_source_message_id = isset($overview->uid) ? $overview->uid : null;
        $date = isset($overview->date) ? $overview->date : null;
        // @todo revist hard coded HTML stripping & decoding
        // strip all html

        $message = trim(strip_tags($message, ""));
        // convert all HTML entities to their applicable characters
        $message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
        if ($message) {
            // Save the message
            return [
                'type' => MessageType::EMAIL,
                'contact_type' => Contact::EMAIL,
                'from' => $from,
                'message' => $message,
                'to' => $to,
                'title' => $title,
                'datetime' => $date,
                'data_source_message_id' => $data_source_message_id,
                'additional_data' => [],
            ];
        }

        return [];
    }

    /**
     * Extract the FROM email address string
     *
     * @param string $from - from address string from email
     * @return string email address or NULL
     */
    protected function getEmail($from)
    {
        $pattern = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i';

        if (preg_match_all($pattern, $from, $emails)) {
            foreach ($emails as $key => $value) {
                if (isset($value[0])) {
                    return $value[0];
                }
            }
        }

        return null;
    }
}
