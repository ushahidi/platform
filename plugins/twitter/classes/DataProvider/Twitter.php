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

class DataProvider_Twitter extends DataProvider {

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Model_Contact::TWITTER;

	public function fetch($limit = FALSE) {
		$since_id = 0;
		$max_allowed_count = 100;

		// XXX: Store id in a group called twitter for now
		$twitter_config = Kohana::$config->load('twitter');

		if ($twitter_config && isset($twitter_config['since_id']))
		{
			$since_id = $twitter_config['since_id'];
		}

		$options = $this->options();

		// Check we have the required config
		if ( !isset($options['consumer_key']) ||
			!isset($options['consumer_secret']) ||
			!isset($options['oauth_access_token']) ||
			!isset($options['oauth_access_token_secret'])
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

		try
		{
			$results = $connection->get("search/tweets", [
				"q" => $this->_construct_get_query($options['twitter_search_terms']),
				"since_id" => $since_id,
				"count" => $limit,
				"result_type" => 'recent'
			]);

			if (! $results->statuses)
			{
				return 0;
			}

			$statuses = $results->statuses;

			$max_id = $statuses[0]->id;

			$count = 0;

			foreach ($statuses as $status) {
				$id = $status->id;
				$user = $status->user;
				$screen_name = $user->screen_name;
				$text = $status->text;

				// @todo Check for similar messages in the database before saving
				$this->receive(Message_Type::TWITTER, $screen_name, $text, $to = NULL, $title = NULL, $id);

				$count++;

			}

			// We shall save the highest id and use it to get messages more
			// recent than this id.
			$twitter_config->set("since_id", $max_id);

		}
		catch (TwitterOAuthException $toe)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
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
}
