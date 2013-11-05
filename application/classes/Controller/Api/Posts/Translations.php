<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Translations Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Posts_Translations extends Controller_Api_Posts {

	protected $_type = 'translation';

	public function _resource()
	{
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