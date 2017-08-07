<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Sets Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_SavedSearches extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'savedsearches';
	}

	protected function _resource()
	{
		return 'savedsearches';
	}

	protected function _is_auth_required()
	{
		if (parent::_is_auth_required())
		{
			// Completely anonymous access is allowed for (some) GET requests.
			// Further checks are made down the stack.
			return ($this->request->method() !== Request::GET);
		}
		return FALSE;
	}

}
