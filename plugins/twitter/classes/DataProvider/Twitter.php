<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Twitter Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Twitter
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Abraham\TwitterOAuth\TwitterOAuth;

use Ushahidi\Core\Entity\Contact;

class DataProvider_Twitter extends DataProvider {

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::TWITTER;

	const MAX_REQUESTS_PER_WINDOW = 180;
	const REQUEST_WINDOW = 900; // Twitter request window in seconds

	private $since_id; // highest id fetched
	private $request_count; // track requests per window

	public function fetch($limit = FALSE) {
		// XXX: Store state in database config for now
		$config = Kohana::$config;
		$this->_initialize($config);

		//Check if data provider is available
		$providers_available = $config->load('features.data-providers');

		if ( !$providers_available['twitter'] )
		{
		  Kohana::$log->add(Log::WARNING, 'The twitter data source is not currently available. It can be accessed by upgrading to a higher Ushahidi tier.');
		  return 0;
		}

		// check if we have reached our rate limit
		if ( !$this->_can_make_request())
		{
			Kohana::$log->add(Log::WARNING, 'You have reached your rate limit for this window');
			return 0;
		}

		$options = $this->options();

		// Check we have the required config
		if ( !isset($options['consumer_key']) ||
			 !isset($options['consumer_secret']) ||
			 !isset($options['oauth_access_token']) ||
			 !isset($options['oauth_access_token_secret']) ||
			 !isset($options['twitter_search_terms'])
		)
		{
			Kohana::$log->add(Log::WARNING, 'Could not fetch messages from twitter, incomplete config');
			return 0;
		}

		$connection = new TwitterOAuth(
			$options['consumer_key'],
			$options['consumer_secret'],
			$options['oauth_access_token'],
			$options['oauth_access_token_secret']
		);

		// Increase curl timeout values
		$connection->setTimeouts(100, 150);

		$count = 0;

		try
		{
			$results = $connection->get("search/tweets", [
				"q" => $this->_construct_get_query($options['twitter_search_terms']),
				"since_id" => $this->since_id,
				"count" => $limit,
				"result_type" => 'recent'
			]);

			if ( empty($results->statuses))
			{
				return 0;
			}

			$statuses = $results->statuses;

			// Store the highest id
			$this->since_id = $statuses[0]->id;

			foreach ($statuses as $status) {
				$id = $status->id;
				$user = $status->user;
				$screen_name = $user->screen_name;
				$text = $status->text;

				// @todo Check for similar messages in the database before saving
				$this->receive(Message_Type::TWITTER, $screen_name, $text, $to = NULL, $title = NULL, $id);

				$count++;
			}

			$this->request_count++; //Increment for successful request

			$this->_update($config);
		}
		catch (TwitterOAuthException $toe)
		{
			Kohana::$log->add(Log::ERROR, $toe->getMessage());
		}
		catch(Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}

		return $count;
	}

	private function _construct_get_query($search_terms)
	{
		return implode(" OR ", array_map('trim', explode(",", $search_terms)));
	}

	private function _can_make_request()
	{
		return  $this->request_count < self::MAX_REQUESTS_PER_WINDOW;
	}

	private function _initialize($config)
	{
		$twitter_config = $config->load('twitter');

		$twitter_config && isset($twitter_config['since_id'])?
							   $this->since_id = $twitter_config['since_id']:
							   $this->since_id = 0;

		$twitter_config && isset($twitter_config['request_count'])?
							   $this->request_count = $twitter_config['request_count']:
							   $this->request_count = 0;

		if ($twitter_config && isset($twitter_config['window_timestamp']))
		{
			$window_has_expired = time() - $twitter_config['window_timestamp'] > self::REQUEST_WINDOW;

			if ($window_has_expired)
			{
				// reset
				$this->request_count = 0;
				$twitter_config->set("window_timestamp", time());
			}
		}
		else
		{
			// save window timestamp for the first time
			$twitter_config->set("window_timestamp", time());
		}
	}

	private function _update($config)
	{
		$twitter_config = $config->load('twitter');
		$twitter_config->set("request_count", $this->request_count);
		$twitter_config->set("since_id", $this->since_id);
	}
}
