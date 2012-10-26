<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
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

class Controller_API_Posts extends Ushahidi_API {

	/**
	 * Create A Post
	 * 
	 * POST /api/posts
	 * 
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post = $this->_request_payload;
		
		$_post = ORM::factory('post')->values($post);
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base post data
			$_post->check();

			// Does post have custom fields included?
			if ( isset($post['values']) )
			{
				// Yes, loop through and validate each value
				// to the form_attribute
				foreach ($post['values'] as $key => $value)
				{
					$attribute = ORM::factory('form_attribute')
						->where('form_id', '=', $post['form_id'])
						->where('key', '=', $key)
						->find();

					$_value = ORM::factory('post_'.$attribute->type)->values(array(
						'value' => $value
						));
					$_value->check();
				}
			}

			// Validates ... so save
			$_post->values($post, array(
				'form_id', 'type', 'title', 'content', 'status'
				));
			$_post->status = (isset($post['status'])) ? $post['status'] : NULL;
			$_post->save();

			if ( isset($post['values']) )
			{
				foreach ($post['values'] as $key => $value)
				{
					$attribute = ORM::factory('form_attribute')
						->where('form_id', '=', $post['form_id'])
						->where('key', '=', $key)
						->find();

					if ( $attribute->loaded() )
					{
						$_value = ORM::factory('post_'.$attribute->type);
						$_value->post_id = $_post->id;
						$_value->form_attribute_id = $attribute->id;
						$_value->value = $value;
						$_value->save();
					}
				}
			}

			// Response is the complete form
			$this->_response_payload = $this->post($_post->id);
		}
		catch (ORM_Validation_Exception $e)
		{
			// Error response
			$this->_response_payload = array(
				'errors' => Arr::flatten($e->errors('models'))
				);
		}
	}

	/**
	 * Retrieve All Posts
	 * 
	 * GET /api/posts
	 * 
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$posts = ORM::factory('post')
			->order_by('created', 'ASC')
			->find_all();

		$count = $posts->count();

		foreach ($posts as $post)
		{
			$results[] = $this->post($post);
		}

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
		);
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
		$post_id = $this->request->param('id', 0);

		// Respond with post
		$post = ORM::factory('post', $post_id);
		$this->_response_payload = $this->post($post);
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
		
	}

	/**
	 * Delete A Post
	 * 
	 * DELETE /api/posts/:id
	 * 
	 * @return void
	 */
	public function action_delete_index()
	{
		$post_id = $this->request->param('id', 0);
		$post = ORM::factory('post', $post_id);
		if ( $post->loaded() )
		{
			$post->delete();
		}
	}

	/**
	 * Retrieve a single post ( ++ Hairy :) )
	 * along with values from attached tables
	 * 
	 * @param $id int - ID of the post
	 * @return array $response
	 * @todo the queries need some optimizing (EAV Fun)
	 */
	public function post($id = 0)
	{
		$response = array();
		$post = ORM::factory('post', $id);
		if ( $post->loaded() )
		{
			$response = array(
				'id' => $id,
				'form_id' => $post->form_id,
				'title' => $post->title,
				'content' => $post->content,
				'status' => $post->status,
				'created' => strtotime($post->created),
				'updated' => strtotime($post->updated),
				'values' => array()
				);

			// Create the Super Union
			$datetimes = DB::select('key', 'value')
				->from('post_datetime')
				->join('form_attributes')
					->on('post_datetime.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $id);

			$decimals = DB::select('key', 'value')
				->union($datetimes)
				->from('post_decimal')
				->join('form_attributes')
					->on('post_decimal.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $id);

			$geometries = DB::select('key', 'value')
				->union($decimals)
				->from('post_geometry')
				->join('form_attributes')
					->on('post_geometry.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $id);

			$ints = DB::select('key', 'value')
				->union($geometries)
				->from('post_int')
				->join('form_attributes')
					->on('post_int.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $id);

			$points = DB::select('key', 'value')
				->union($ints)
				->from('post_point')
				->join('form_attributes')
					->on('post_point.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $id);

			$texts = DB::select('key', 'value')
				->union($points)
				->from('post_text')
				->join('form_attributes')
					->on('post_text.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $id);

			$results = DB::select('key', 'value')
				->union($texts)
				->from('post_varchar')
				->join('form_attributes')
					->on('post_varchar.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $id)
				->execute();

			foreach ($results as $result)
			{
				$response['values'][$result['key']] = $result['value'];
			}
		}

		return $response;
	}
}
