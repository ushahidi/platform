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
		
		$parent = ORM::factory('Post', $this->_parent_id);
		
		if ( ! $parent->loaded())
		{
			throw new HTTP_Exception_404('Post does not exist. Post ID: \':id\'', array(
				':id' => $this->_parent_id,
			));
		}
	}

	/**
	 * Retrieve A Post
	 * 
	 * GET /api/posts/:id
	 * 
	 * @return void
	 */
	public function action_get_index()
	{
		// If we've got a post_id just forward this to the parent controller
		if ($post_id = $this->request->param('id', 0)) return parent::action_get_index();
		
		// Otherwise try locale
		
		if ($post_locale = $this->request->param('locale', FALSE))
		{
			$post = ORM::factory('Post')
				->where('locale', '=', $post_locale)
				->where('parent_id', '=', $this->_parent_id)
				->find();

			if (! $post->loaded())
			{
				throw new HTTP_Exception_404('Translation does not exist. Locale: \':locale\'', array(
					':locale' => $post_locale,
				));
			}

			$this->_response_payload = $post->for_api();
		}
	}

	/**
	 * Update A Post
	 * 
	 * PUT /api/posts/:id
	 * 
	 * @return void
	 */
	public function action_put_index()
	{
		// If we've got a post_id just forward this to the parent controller
		if ($post_id = $this->request->param('id', 0)) return parent::action_put_index();
		
		// Otherwise try locale
		
		if ($post_locale = $this->request->param('locale', FALSE))
		{
			$post = $this->_request_payload;
	
			// unpack form to get form_id
			if (isset($post['form']))
			{
				if (is_array($post['form']) AND isset($post['form']['id']))
				{
					$post['form_id'] = $post['form']['id'];
				}
				elseif (is_numeric($post['form']))
				{
					$post['form_id'] = $post['form'];
				}
			}
	
			$_post = ORM::factory('Post', array(
				'locale' => $post_locale,
				'parent_id' => $this->_parent_id
			));
	
			if (! $_post->loaded())
			{
				throw new HTTP_Exception_404('Translation does not exist. Locale: \':locale\'', array(
					':locale' => $post_locale,
				));
			}
			
			$this->create_or_update_post($_post, $post);
			
		}
	}

	/**
	 * Delete A Post
	 * 
	 * DELETE /api/posts/:id/translations/:locale
	 * 
	 * @return void
	 */
	public function action_delete_index()
	{
		// If we've got a post_id just forward this to the parent controller
		if ($post_id = $this->request->param('id', 0)) return parent::action_delete_index();
		
		// Otherwise try locale
		if ($post_locale = $this->request->param('locale', FALSE))
		{
			$post = ORM::factory('Post', array(
				'locale' => $post_locale,
				'parent_id' => $this->_parent_id
			));
			
			$this->_response_payload = array();
			if ( $post->loaded() )
			{
				// Return the post we just deleted (provides some confirmation)
				$this->_response_payload = $post->for_api();
				$post->delete();
			}
			else
			{
				throw new HTTP_Exception_404('Translation does not exist. Locale: \':locale\'', array(
					':locale' => $post_locale,
				));
			}
		}
	}
}