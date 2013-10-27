<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Streams Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Posts_Updates extends Controller_Api_Posts {

	public function before()
	{
		parent::before();

		// Parent ID of this Stream Post
		$this->_parent_id = $this->request->param('post_id', 0);
		
		$parent = ORM::factory('Post', $this->_parent_id);
		
		if ( ! $parent->loaded())
		{
			throw new HTTP_Exception_404('Parent Post does not exist. Post ID: \':id\'', array(
				':id' => $this->_parent_id,
			));
		}
	}
}