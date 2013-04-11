<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Translations Controller
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

class Controller_Api_Posts_Translations extends Controller_Api_Posts {

	protected $_type = 'translation';

	public function before()
	{
		parent::before();

		// Parent ID of this Stream Post
		$this->_parent_id = $this->request->param('post_id', 0);
	}
}