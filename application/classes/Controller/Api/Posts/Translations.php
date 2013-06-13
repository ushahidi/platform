<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Translations Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Api_Posts_Translations extends Controller_Api_Posts {

	protected $_type = 'translation';

	public function _resource()
	{
		// Parent ID of this Stream Post
		$this->_parent_id = $this->request->param('post_id', 0);
		
		// Check parent post exists
		$parent = ORM::factory('Post', $this->_parent_id);
		if ( ! $parent->loaded())
		{
			throw new HTTP_Exception_404('Parent Post does not exist. Post ID: \':id\'', array(
				':id' => $this->_parent_id,
			));
		}
		
		// Normal post loading: dummy post, then by id
		parent::_resource();
		
		// Load post by locale
		if ($post_locale = $this->request->param('locale', FALSE))
		{
			$post = ORM::factory('Post')
				->where('locale', '=', $post_locale)
				->where('parent_id', '=', $this->_parent_id)
				->where('type', '=', $this->_type)
				->find();

			if (! $post->loaded())
			{
				throw new HTTP_Exception_404('Translation does not exist. Locale: \':locale\'', array(
					':locale' => $post_locale,
				));
			}
			
			$this->_resource = $post;
		}
	}
}