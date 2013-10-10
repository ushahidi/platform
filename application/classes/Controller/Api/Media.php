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

	// Resize to these dimentions
	protected $width_medium = 824;

	protected $width_thumbnail = 70;

	protected $upload_dir;

	public function before()
	{
		parent::before();

		// Initialize the uploads directory.
		$this->upload_dir = DOCROOT.'uploads/';
	}

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

		foreach ($media as $m) {
			$results[] = $m->for_api();

		}

		// Current/Next/Prev urls
		$params = array(
				'limit' => $this->record_limit,
				'offset' => $this->record_offset,
		);

		// Only add order/orderby if they're already set
		if ($this->request->query('orderby') OR $this->request->query('order')) {
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

		if ( ! $media->loaded()) {
			throw new HTTP_Exception_404('Media does not exist. Media ID \':id\'', array(
				':id' => $media_id,
			));
		}
		$this->_response_payload = $media->for_api();
	}

	/**
	 * Create a media
	 *
	 * POST /api/meida
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		// Validation object for additional validation (not in model)
		$media_data = Validation::factory(array_merge($_FILES,$this->request->post()))
			->rule('file', 'not_empty')
			->rule('file','Upload::valid')
			->rule('file','Upload::type',array(':value',array('gif','jpg','jpeg','png')))
			->rule('file','Upload::size', [':value', '1M']);

		try
		{
			// Validate base post data
			if ($media_data->check() === FALSE)
			{
				throw new ORM_Validation_Exception('media_value', $media_data);
			}

			// Upload the file
			$file = upload::save($media_data['file'], NULL, $this->upload_dir);

			$filename = strtolower(Text::random('alnum', 3))."_".time();

			// Save original size
			$o_image = Image::factory($file);
			$o_image->save($this->upload_dir.$filename."_o.jpg");

			if ($o_image->width < $this->width_medium)
			{
				$this->width_medium = $o_image->width;
			}
			// Resize original file to a medium size
			$m_image = Image::factory($file);
			$m_image->resize($this->width_medium,NULL,Image::AUTO)
				->save($this->upload_dir.$filename."_m.jpg");

			// Resize original file to a thumbnail size
			if ($m_image->width < $this->width_thumbnail)
			{
				$this->width_thumbnail = $m_image->width;
			}

			$t_image = Image::factory($file);
			$t_image->resize($this->width_thumbnail,NULL,Image::AUTO)
				->save($this->upload_dir.$filename."_t.jpg");

			// Remove the temporary file
			Unlink($file);

			// Save details to the database
			$media = ORM::factory('Media');

			// Set original details
			$media->o_width = $o_image->width;
			$media->o_height = $o_image->height;
			$media->o_filename = $filename."_o.jpg";

			// Set medium details
			$media->m_filename = $filename."_m.jpg";
			$media->m_width = $m_image->width;
			$media->m_height = $m_image->height;

			// Set thumbnail details
			$media->t_filename = $filename."_t.jpg";
			$media->t_width = $t_image->width;
			$media->t_height = $t_image->height;


			// Set caption if it is set
			if (isset($media_data['caption']))
			{
				$media->caption = $media_data['caption'];
			}

			// Save file url
			$media->file_url = URL::site('uploads',Request::current());

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

			// Delete files from disk
			// Delete the original size file
			unlink($this->upload_dir.$media->o_filename);

			// Delete the medium size file
			unlink($this->upload_dir.$media->m_filename);

			// Delete the thumbnail size file
			unlink($this->upload_dir.$media->t_filename);

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

	/**
	 * Override the parent parse request body implementation so it doesn't
	 * ask for JSON body when action_post_index_collection is implemented
	 *
	 * @return
	 */
	protected function _parse_request_body()
	{
		if ($this->request->action() === "post_index_collection")
		{
			RETURN;
		}
		parent::_parse_request_body();
	}
}
