<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Sets Controller
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

class Controller_Api_Sets extends Ushahidi_Api {

	/**
	 * Create A Set
	 * 
	 * POST /api/sets
	 * 
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post = $this->_request_payload;
		
		$set = ORM::factory('Set')->values($post, array(
			'name', 'filter'
			));
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base set data
			$set->check();

			// Validates ... so save
			$set->values($post, array(
				'name', 'filter'
				));
			$set->save();

			// Response is the set
			$this->_response_payload = $set->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new Http_Exception_400('Validation Error: \':errors\'', array(
				'errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}

	/**
	 * Retrieve All Sets
	 * 
	 * GET /api/sets
	 * 
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$sets = ORM::factory('Set')
			->order_by('created', 'ASC')
			->find_all();

		$count = $sets->count();

		foreach ($sets as $set)
		{
			$results[] = $set->for_api();
		}

		// Respond with sets
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
	}

	/**
	 * Retrieve A Set
	 * 
	 * GET /api/sets/:id
	 * 
	 * @return void
	 */
	public function action_get_index()
	{
		$set_id = $this->request->param('id', 0);

		// Respond with set
		$set = ORM::factory('Set', $set_id);

		if (! $set->loaded() )
		{
			throw new Http_Exception_404('Set does not exist. Set ID \':id\'', array(
				':id' => $set_id,
			));
		}

		$this->_response_payload = $set->for_api();
	}

	/**
	 *@TODO
	 * Retrieve sets with search params
	 *
	 * GET /api/sets/:params
	 *
	 *@return void
	 */


	/**
	 * Update A Set
	 * 
	 * PUT /api/sets/:id
	 * 
	 * @return void
	 */
	public function action_put_index()
	{
		$set_id = $this->request->param('id', 0);
		$post = $this->_request_payload;
		
		$set = ORM::factory('Set', $set_id)->values($post, array(
			'name', 'filter'
			));

		if (! $set->loaded() )
		{
			throw new Http_Exception_404('Set does not exist. Set ID \':id\'', array(
				':id' => $set_id,
			));
		}
		
		// Position Set id to ensure sane response if set doesn't exist yet.
		$set->id = $set_id;
		
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base set data
			$set->check();


			// Validates ... so save
			$set->values($post, array(
				'name', 'filter'
				));
			$set->save();


			// Response is the complete set
			$this->_response_payload = $set->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new Http_Exception_400('Validation Error: \':errors\'', array(
				'errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}

	/**
	 * Delete A Set
	 * 
	 * DELETE /api/sets/:id
	 * 
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$set_id = $this->request->param('id', 0);
		$set = ORM::factory('Set', $set_id);
		$this->_response_payload = array();
		if ( $set->loaded() )
		{
			// Return the set we just deleted (provides some confirmation)
			$this->_response_payload = $set->for_api();
			$set->delete();
		}
		else
		{
			throw new Http_Exception_404('Set does not exist. Set ID: \':id\'', array(
				':id' => $set_id,
			));
		}
	}
}
