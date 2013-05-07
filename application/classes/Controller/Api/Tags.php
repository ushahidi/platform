<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Tags Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Api_Tags extends Ushahidi_Api {

	/**
	 * Create A Tag
	 * 
	 * POST /api/tags
	 * 
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post = $this->_request_payload;
		
		$tag = ORM::factory('Tag');
		
		$this->create_or_update_tag($tag, $post);
	}

	/**
	 * Retrieve All Tags
	 * 
	 * GET /api/tags
	 * 
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$tags = ORM::factory('Tag')
			->order_by('created', 'ASC')
			->find_all();

		$count = $tags->count();

		foreach ($tags as $tag)
		{
			$results[] = $tag->for_api();
		}

		// Respond with forms
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
	}

	/**
	 * Retrieve A Tag
	 * 
	 * GET /api/tags/:id
	 * 
	 * @return void
	 */
	public function action_get_index()
	{
		$id = $this->request->param('id', 0);

		// Respond with form
		$tag = ORM::factory('Tag', $id);

		if (! $tag->loaded() )
		{
			throw new HTTP_Exception_404('Tag does not exist. ID \':id\'', array(
				':id' => $id,
			));
		}

		$this->_response_payload = $tag->for_api();
	}

	/**
	 * Update A Tag
	 * 
	 * PUT /api/tags/:id
	 * 
	 * @return void
	 */
	public function action_put_index()
	{
		$id = $this->request->param('id', 0);
		$post = $this->_request_payload;
		
		$tag = ORM::factory('Tag', $id);
		
		if (! $tag->loaded())
		{
			throw new HTTP_Exception_404('Tag does not exist. ID: \':id\'', array(
				':id' => $id,
			));
		}
		
		$this->create_or_update_tag($tag, $post);
	}

	/**
	 * Delete A Tag
	 * 
	 * DELETE /api/tags/:id
	 * 
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$id = $this->request->param('id', 0);
		$tag = ORM::factory('Tag', $id);
		$this->_response_payload = array();
		if ( $tag->loaded() )
		{
			// Return the form we just deleted (provides some confirmation)
			$this->_response_payload = $tag->for_api();
			$tag->delete();
		}
		else
		{
			throw new HTTP_Exception_404('Tag does not exist. ID: \':id\'', array(
				':id' => $id,
			));
		}
	}
	
	/**
	 * Save tags
	 * 
	 * @param Tag_Model $tag
	 * @param array $post POST data
	 */
	protected function create_or_update_tag($tag, $post)
	{
		$tag->values($post, array(
			'tag', 'slug', 'type', 'parent_id', 'priority'
			));
		
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base form data
			$tag->check();

			// Validates ... so save
			$tag->values($post, array(
				'tag', 'slug', 'type', 'parent_id', 'priority'
				));
			$tag->save();

			// Response is the complete form
			$this->_response_payload = $tag->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				'errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}
}
