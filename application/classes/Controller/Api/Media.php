<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi API Media Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.come
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Api_Media extends Ushahidi_Api {

	/**
	 * @var array List of HTTP methods which support body content
	 */
	protected $_methods_with_body_content = array
	(
		Http_Request::PUT,
	);

	/**
	 * Retrieve all media
	 *
	 * GET /api/media
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$this->prepare_order_limit_params();

		// Query media table
		$media_query = ORM::factory('Media')
				->order_by($this->record_orderby, $this->record_order)
				->offset($this->record_offset)
				->limit($this->record_limit);

		$media = $media_query->find_all();

		$count = $media->count();

		foreach ($media as $m)
		{
			$results[] = $m->for_api();

		}

		// Current/Next/Prev urls
		$params = array(
				'limit' => $this->record_limit,
				'offset' => $this->record_offset,
		);

		// Only add order/orderby if they're already set
		if ($this->request->query('orderby') OR $this->request->query('order'))
		{
			$params['orderby'] = $this->record_orderby;
			$params['order'] = $this->record_order;
		}

		$prev_params = $next_params = $params;
		$next_params['offset'] = $params['offset'] + $params['limit'];
		$prev_params['offset'] = $params['offset'] - $params['limit'];
		$prev_params['offset'] = ($prev_params['offset'] > 0) ? $prev_params['offset'] : 0;

		$curr = URL::site($this->request->uri().URL::query($params), $this->request);
		$next = URL::site($this->request->uri().URL::query($next_params), $this->request);
		$prev = URL::site($this->request->uri().URL::query($prev_params), $this->request);

		// Respond with media details
		$this->_response_payload = array(
				'count' => $count,
				'results' => $results,
				'limit' => $this->record_limit,
				'offset' => $this->record_offset,
				'order' => $this->record_order,
				'orderby' => $this->record_orderby,
				'curr' => $curr,
				'next' => $next,
				'prev' => $prev,
		);
	}

	/**
	 * Retrieve a Media
	 *
	 * GET /api/media/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$media_id = $this->request->param('id', 0);

		// Query media table
		$media = ORM::factory('Media', $media_id);

		if ( ! $media->loaded())
		{
			throw new HTTP_Exception_404('Media does not exist. Media ID \':id\'', array(
				':id' => $media_id,
			));
		}
		$this->_response_payload = $media->for_api();
	}

	/**
	 * Create a media
	 *
	 * POST /api/media
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		// Validation object for additional validation (not in model)
		$max_file_size = Kohana::$config->load('media.max_file_upload_size');
		$media_data = Validation::factory(array_merge($_FILES,$this->request->post()))
			->rule('file', 'not_empty')
			->rule('file','Upload::valid')
			->rule('file','Upload::type', array(':value', array('gif','jpg','jpeg','png')))
			->rule('file','Upload::size', [':value', $max_file_size]);
		try
		{
			// Validate base post data
			if ($media_data->check() === FALSE)
			{
				throw new ORM_Validation_Exception('media_value', $media_data);
			}

			$upload_dir = Kohana::$config->load('media.media_upload_dir');

			// Make media/uploads/ directory if it doesn't exist
			if ( ! file_exists($upload_dir))
			{
				// Make directory recursively
				mkdir($upload_dir, 0755, TRUE);
			}

			// Upload the file
			$file = upload::save($media_data['file'], NULL, $upload_dir);

			$filename = strtolower(Text::random('alnum', 3))."_".time();

			// Save original size
			$o_image = Image::factory($file);

			$o_image->save($upload_dir.$filename."_o.jpg");

			if (file_exists($file))
			{
				// Remove the temporary file
				Unlink($file);
			}

			// Save details to the database
			$media = ORM::factory('Media');

			// Set original details
			$media->o_width = $o_image->width;
			$media->o_height = $o_image->height;
			$media->o_filename = $filename."_o.jpg";

			// Set mime type
			$media->mime = $o_image->mime;

			// Set caption if it is set
			if (isset($media_data['caption']))
			{
				$media->caption = $media_data['caption'];
			}

			// Save details to the database
			$media->save();

			// Return the newly created media
			$this->_response_payload = $media->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models')))
				));
		}
	}

	/**
	 * Delete a media
	 *
	 * DELETE /api/media/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$media_id = $this->request->param('id', 0);

		$media = ORM::factory('Media', $media_id);

		$this->_response_payload = array();

		if ($media->loaded())
		{
			// Return the media that is about to be deleted
			$this->_response_payload = $media->for_api();

			// Delete the details from the db
			$media->delete();
		}
		else
		{
			throw new HTTP_Exception_404('Media does not exist. Media ID: \':id\'', array(
				':id' => $media_id,
			));
		}
	}
}
