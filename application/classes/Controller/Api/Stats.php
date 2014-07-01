<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Stats Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Stats extends Ushahidi_Api {

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'stats';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'stats';
	}

	/**
	 * Get a count of tags,posts,sets,users
	 *
	 * GET /api/stats
	 *
	 * @return void
	 */

	public function action_get_index_collection()
	{
		$counts = array();

		// @todo all of these queries should be limited to only the things the
		// user can see. but cycling over every id in the system is probably not
		// a valid solution either.
		$base_query = DB::select([DB::expr('COUNT(*)'), 'total']);

		$tags = clone $base_query;
		$tags->from('tags');

		// @todo this most important limitation is probably on post stats.
		$posts = clone $base_query;
		$posts->from('posts');

		// @todo exposing the number of users may not be ideal either...
		$users = clone $base_query;
		$users->from('users');

		// @todo message totals should definitely only be available to admins...
		$messages = clone $base_query;
		$messages->from('messages')->where('direction', '=', 'incoming');

		$stats = array();
		foreach(compact('tags', 'posts', 'users', 'messages') as $name => $query)
		{
			$total = $query->execute()->get('total');
			$counts[$name]['all'] = $total;
		}

		// post stats
		$status_query = clone $posts;
		$status_query->select('status')->group_by('status');

		// add post status totals
		$counts['posts'] += $status_query->execute()->as_array('status', 'total');

		// message stats
		$type_query = clone $messages;
		$type_query->select('type')->group_by('type')
			->where('type', 'in', ['sms', 'email']);

		// add message type totals
		$counts['messages'] += $type_query->execute()->as_array('type', 'total');

		$status_query = clone $messages;
		$status_query->select('status')->group_by('status')
			->where('status', 'in', ['received', 'archived']);

		// add message status totals
		$counts['messages'] += $status_query->execute()->as_array('status', 'total');

		// Respond with counts
		$this->_response_payload = $counts;
	}

}
