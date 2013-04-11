<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Revisions Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Api_Posts_Revisions extends Controller_Api_Posts {

	protected $_type = 'revision';

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET    => 'get',
	);

	public function before()
	{
		parent::before();

		// Parent ID of this Stream Post
		$this->_parent_id = $this->request->param('post_id', 0);
		
		$parent = ORM::factory('Post', $this->_parent_id);
		
		if ( ! $parent->loaded())
		{
			throw new Http_Exception_404('Post does not exist. Post ID: \':id\'', array(
				':id' => $this->_parent_id,
			));
		}
	}
}