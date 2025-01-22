<?php

namespace Ushahidi\DataSource\Email;

/**
 * Email Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Email
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\Contracts\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Mail\Mailer;
use Ushahidi\DataSource\Contracts\IncomingDataSource;
use Ushahidi\DataSource\Concerns\MapsInboundFields;
use Ushahidi\Contracts\Repository\Entity\MessageRepository;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\DataSource\Contracts\MessageType;

class Email extends OutgoingEmail implements IncomingDataSource
{
    use MapsInboundFields;

    protected $config;
    protected $mailer;
    protected $messageRepo;
    protected $configRepo;

    /**
     * Constructor function for DataSource
     * @param array $config
     * @param Mailer|null $mailer
     * @param MessageRepository|null $messageRepo
     * @param ConfigRepository|null $configRepo
     */
    public function __construct(
        array $config,
        Mailer $mailer = null,
        MessageRepository $messageRepo = null,
        ConfigRepository $configRepo = null
    ) {
        $this->config = $config;
        $this->mailer = $mailer;
        $this->messageRepo = $messageRepo;
        $this->configRepo = $configRepo;
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
            ],
            'incoming_all_unread' => [
                'label' => 'Fetch Emails',
                'input' => 'radio',
                'description' => 'Fetch every email from the inbox, or only unread.',
                'options' => ['All', 'Unread'],
                'rules' => ['required']
            ],
            'incoming_last_uid' => [
                'label' => '',
                'input' => 'hidden',
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
            Log::error("imap extension not enabled");
            return [];
        }

        $messages = [];

        $limit = 200;

        $type = $this->config['incoming_type'] ?? '';
        $server = $this->config['incoming_server'] ?? '';
        $port = $this->config['incoming_port'] ?? '';
        $encryption = $this->config['incoming_security'] ?? '';
        $username = $this->config['incoming_username'] ?? '';
        $password = $this->config['incoming_password'] ?? '';
        $unread_only = $this->config['incoming_all_unread'] ?? 'Unread';
        $last_uid = $this->config['incoming_last_uid'] ?? '';
        $new_last_uid = 0;

        // Encryption type
        $encryption = (strcasecmp($encryption, 'none') != 0) ? '/'.$encryption : '';

        // To connect to an SSL IMAP or POP3 server with a self-signed certificate,
        // add /novalidate-cert after the encryption protocol specification:
        $no_cert_validation = !empty($encryption) ? '/novalidate-cert' : '';

        try {
            // Try to connect
            $inbox = '{'.$server.':'.$port.'/'.$type.$encryption.$no_cert_validation.'}INBOX';
            $connection = @imap_open($inbox, $username, $password, 0, 1);

            $errors = imap_errors();
            $alerts = imap_alerts();

            // Return on connection error
            if (! $connection || $errors || $alerts) {
                $errors = is_array($errors) ? implode(', ', $errors) : "";
                $alerts = is_array($alerts) ? implode(', ', $alerts) : "";
                Log::info("Could not connect to incoming email server", compact('errors', 'alerts'));
                return [];
            }

            $mailboxinfo = imap_check($connection);

            Log::info("Connected to $inbox", [$mailboxinfo]);

            // Allow an existing installation to transition to config based without forcing the platform to download everything again.
            if ($last_uid == '') {
                $last_uid = $this->messageRepo->getLastUID('email');
            }

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

                    if ($unread_only == 'Unread' and $email->seen == 1) {
                        continue;
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
                    if (isset($email->uid)) {
                        $new_last_uid = isset($email->uid) ? $email->uid : null;
                    }
                }
            }

            if ($new_last_uid && $new_last_uid != $last_uid) {
                $this->updateLastUid($this->config, $new_last_uid);
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

    private function updateLastUid($config, $last_uid)
    {
        $providerConfig = $this->configRepo->get('data-provider');
        $config['incoming_last_uid'] = $last_uid;
        $providerConfigArray = $providerConfig->asArray();
        $providerConfigArray[$this->getId()] = $config;
        $providerConfig->setState($providerConfigArray);
        $this->configRepo->update($providerConfig);
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
