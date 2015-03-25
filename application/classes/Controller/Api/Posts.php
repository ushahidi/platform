<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase;

class Controller_Api_Posts extends Ushahidi_Rest {

	/**
	 * @var int Post Parent ID
	 */
	protected $_parent_id = NULL;

	/**
	 * @var string Post Type
	 */
	protected $_type = 'report';

	// Ushahidi_Rest
	protected function _scope()
	{
		return 'posts';
	}

	protected $_boundingbox = FALSE;

	// Ushahidi_Rest
	protected function _identifiers()
	{
		return parent::_identifiers() + [
			'type'      => $this->_type
		];
	}

	// Ushahidi_Rest
	protected function _filters()
	{
		return parent::_filters() + [
			'type'      => $this->_type,
			'parent'    => $this->request->param('parent_id', null),
		];
	}

	// Ushahidi_Rest
	protected function _payload()
	{
		return parent::_payload() + [
			'type'      => $this->_type,
			'parent_id' => $this->request->param('parent_id', null),
		];
	}

	/**
	 * Create A Post
	 *
	 * POST /api/posts
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		parent::action_post_index_collection();

		// Ensure identifiers are set for parent checks
		$this->_usecase
			->setIdentifiers($this->_identifiers());
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
		parent::action_get_index_collection();

		// Ensure identifiers are set for parent checks
		$this->_usecase
			->setIdentifiers($this->_identifiers());
	}

	public function action_get_stats_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'stats')
			// @todo allow injecting formatters based on resource + action
			->setFormatter(service('formatter.entity.post.stats'))
			->setFilters($this->_filters());
	}

	public function action_options_stats_collection()
	{
		$this->action_options_index_collection();
	}
}
