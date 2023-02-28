<?php

namespace Ushahidi\DataSource\Twitter;

/**
 * Twitter Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Twitter
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\Contracts\Contact;
use Illuminate\Support\Facades\Log;
use Symm\Gisconverter\Decoders\WKT;
use Abraham\TwitterOAuth\TwitterOAuth;
use Symm\Gisconverter\Decoders\GeoJSON;
use Ushahidi\DataSource\Contracts\MessageType;
use Abraham\TwitterOAuth\TwitterOAuthException;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Contracts\IncomingDataSource;
use Ushahidi\DataSource\Contracts\OutgoingDataSource;
use Ushahidi\DataSource\Concerns\MapsInboundFields;
use Ushahidi\Core\Entity\ConfigRepository;

class Twitter implements IncomingDataSource, OutgoingDataSource
{
    use MapsInboundFields;

    const MAX_REQUESTS_PER_WINDOW = 180;
    const REQUEST_WINDOW = 900; // Twitter request window in seconds

    protected $since_id; // highest id fetched
    protected $request_count; // track requests per window
    protected $search_terms;
    protected $window_timestamp;
    protected $config;
    protected $configRepo;

    /**
     * Constructor function for DataSource
     */
    public function __construct(array $config, ConfigRepository $configRepo = null, \Closure $connectionFactory = null)
    {
        $this->config = $config;
        $this->connectionFactory = $connectionFactory;
        $this->configRepo = $configRepo;
    }

    public function getName()
    {
        return 'Twitter';
    }

    public function getId()
    {
        return strtolower($this->getName());
    }

    public function getServices()
    {
        return [MessageType::TWITTER];
    }

    public function getOptions()
    {
        return [
            'intro_step1' => [
                'label' => 'Step 1: Create a new Twitter application',
                'input' => 'read-only-text',
                'description' => function () {
                    return 'Twitter applications may take some time to be approved by Twitter.
                     Please be aware of this if you need this data quickly.
                    <br><br>Create your <a href="https://developer.twitter.com/en/apps/create" target="_blank">
                    Twitter application here</a>.';
                }
            ],
            // @todo figure out how to inject link and fix base url
            'intro_step2' => [
                'label' => 'Step 2: Generate a consumer key and secret',
                'input' => 'read-only-text',
                'description' => function () {
                    return 'Once you\'ve created the application click on "Keys and Access Tokens".<br />
						Then click "Generate Consumer Key and Secret".<br />
						Copy keys, tokens and secrets into the fields below.';
                }
            ],
            'consumer_key' => [
                'label' => 'Consumer Key',
                'input' => 'text',
                'description' => 'Add the consumer key from your Twitter app. ',
                'rules' => ['required']
            ],
            'consumer_secret' => [
                'label' => 'Consumer Secret',
                'input' => 'text',
                'description' => 'Add the consumer secret from your Twitter app.',
                'rules' => ['required']
            ],
            'oauth_access_token' => [
                'label' => 'Access Token',
                'input' => 'text',
                'description' => 'Add the access token you generated for your Twitter app.',
                'rules' => ['required']
            ],
            'oauth_access_token_secret' => [
                'label' => 'Access Token Secret',
                'input' => 'text',
                'description' => 'Add the access secret that you generated for your Twitter app.',
                'rules' => ['required']
            ],
            'twitter_search_terms' => [
                'label' => 'Twitter search terms',
                'input' => 'text',
                'description' => 'Add search terms separated with commas',
                'rules' => ['required']
            ]
        ];
    }

    public function getInboundFields()
    {
        return [
            'Date' => 'datetime',
            'Message' => 'text'
        ];
    }

    public function isUserConfigurable()
    {
        return true;
    }

    // DataSource
    public function fetch($limit = false)
    {
        $this->initialize();
        // Check we have the required config
        if (!isset($this->config['twitter_search_terms'])) {
            Log::warning('Could not fetch messages from twitter, incomplete config');
            return [];
        }

        if ($limit === false) {
            $limit = 50;
        }

        $connection = $this->connect();
        if (!$connection) {
            // The connection didn't succeed, but this is not fatal to the application flow
            // Just return 0 messages fetched
            return [];
        }
        $connection->setDecodeJsonAsArray(true);
        $messages = [];

        try {
            $results = $connection->get("search/tweets", [
                "q" => $this->constructGetQuery($this->search_terms),
                "since_id" => $this->since_id,
                "count" => $limit,
                "result_type" => 'recent'
            ]);

            if (empty($results['statuses'])) {
                return [];
            }

            $statuses = $results['statuses'];

            // Store the highest id
            $this->since_id = $statuses[0]['id'];

            foreach ($statuses as $status) {
                $id = $status['id'];
                $user = $status['user'];
                $date = $status['created_at'];

                // Skip retweets
                if (array_key_exists('retweeted_status', $status) &&
                    array_key_exists('text', $status['retweeted_status'])
                ) {
                    continue;
                }
                $user_id = $user['id_str'];
                // @todo Check for similar messages in the database before saving
                /***
                 * Twitter links note: (message field)
                 * Best compromise I could find was just make the
                 * proper urls with user_id rather than only
                 * tweet id (for which there is an unofficial formula)...
                 * since there doesn't seem to be a way to grab the URL from the
                 * API itself in the v1.1 search endpoint.
                 * Fun fact: if the user id is wrong, twitter
                 * still takes you to the correct Tweet...
                 * they just use the tweet id
                **/
                $messages[] = [
                    'type' => MessageType::TWITTER,
                    'contact_type' => Contact::TWITTER,
                    'from' => $user_id,
                    'to' => null,
                    'message' => "https://twitter.com/$user_id/status/$id",
                    'title' => 'From twitter on ' .  $date,
                    'datetime' => $date,
                    'data_source_message_id' => $id,
                    'additional_data' => []
                ];
            }

            $this->request_count++; //Increment for successful request

            $this->update();
        } catch (TwitterOAuthException $toe) {
            Log::error($toe->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $messages;
    }

    // DataSource
    public function send($to, $message, $title = '', $contact_type = null)
    {
        $connection = $this->connect();

        if (!$connection) {
            return [MessageStatus::FAILED, false];
        }

        try {
            $response = $connection->post("statuses/update", [
                "status" => '@' . $to . ' ' . $message
            ]);

            if (!isset($response->id)) {
                Log::error("Twitter: Send failed", ['response' => $response]);
                return [MessageStatus::FAILED, false];
            }
            return [MessageStatus::SENT, $response->id];
        } catch (TwitterOAuthException $e) {
            Log::error($e->getMessage());
            return [MessageStatus::FAILED, false];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [MessageStatus::FAILED, false];
        }
    }

    private function constructGetQuery($search_terms)
    {
        return implode(" OR ", array_map('trim', explode(",", $search_terms)));
    }

    private function canMakeRequest()
    {
        return  $this->request_count < self::MAX_REQUESTS_PER_WINDOW;
    }

    private function initialize()
    {
        $twitterConfig = $this->configRepo->get('twitter');

        isset($twitterConfig->since_id) ?
                               $this->since_id = $twitterConfig->since_id:
                               $this->since_id = 0;

        $this->search_terms = isset($this->config['twitter_search_terms']) ? $this->config['twitter_search_terms']: "";

        // If search terms have changed, reset since_id
        if ($this->search_terms !== $twitterConfig->search_terms) {
            $this->since_id = 0;
        }

        $twitterConfig && isset($twitterConfig->request_count)?
                               $this->request_count = $twitterConfig->request_count:
                               $this->request_count = 0;

        if (isset($twitterConfig->window_timestamp)) {
            $this->window_timestamp = $twitterConfig->window_timestamp;
            $window_has_expired = time() - $twitterConfig->window_timestamp > self::REQUEST_WINDOW;

            if ($window_has_expired) {
                // reset
                $this->request_count = 0;
                $this->window_timestamp = time();
            }
        } else {
            // save window timestamp for the first time
            $this->window_timestamp = time();
        }
    }

    private function update()
    {
        // XXX: Store state in database config for now
        $twitterConfig = $this->configRepo->get('twitter');

        $twitterConfig->setState([
            'request_count' => $this->request_count,
            'since_id' => $this->since_id,
            'search_terms' => $this->search_terms,
            'window_timestamp' => $this->window_timestamp
        ]);

        $this->configRepo->update($twitterConfig);
    }

    private function connect()
    {
        // check if we have reached our rate limit
        if (!$this->canMakeRequest()) {
            Log::warning('You have reached your rate limit for this window');
            return;
        }
            // Check we have the required config
        if (!isset($this->config['consumer_key']) ||
             !isset($this->config['consumer_secret']) ||
             !isset($this->config['oauth_access_token']) ||
             !isset($this->config['oauth_access_token_secret'])
        ) {
            Log::warning('Could not connect to twitter, incomplete config');
            return;
        }

        $connection = ($this->connectionFactory)(
            $this->config['consumer_key'],
            $this->config['consumer_secret'],
            $this->config['oauth_access_token'],
            $this->config['oauth_access_token_secret']
        );

        if (!($connection instanceof TwitterOAuth)) {
            throw new \Exception("Client is not an instance of TwitterOAuth");
        }

        // Increase curl timeout values
        $connection->setTimeouts(100, 150);
        return $connection;
    }
}
