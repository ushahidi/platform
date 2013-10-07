<?php defined('SYSPATH') OR die('No direct access allowed.');

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
	 * @var string Field to sort results by
	 */
	 protected $record_orderby = 'created';

	/**
	 * @var string Direct to sort results
	 */
	 protected $record_order = 'DESC';

	/**
	 * @var int Maximum number of results to return
	 */
	 protected $record_allowed_orderby = array('id', 'created');

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
		$prev_params['offset'] = $prev_params['offset'] > 0 ? $prev_params['offset'] : 0;

		$curr = URL::site($this->request->uri() . URL::query($params), $this->request);
		$next = URL::site($this->request->uri() . URL::query($next_params), $this->request);
		$prev = URL::site($this->request->uri() . URL::query($prev_params), $this->request);

		//Respond with media details
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
			throw new HTTP_Exception_404('Set does not exist. Set ID \':id\'', array(
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
	public function action_post_index()
	{

	}

	/**
	 * Delete a media
	 *
	 * DELETE /api/sets/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{

	}
}