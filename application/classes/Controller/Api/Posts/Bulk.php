<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Bulk Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase;

class Controller_Api_Posts_Bulk extends Ushahidi_Rest {

	/**
	 * @var int Post Parent ID
	 */
	protected $_parent_id = NULL;

	/**
	 * @var string Post Type
	 */
	protected $_type = 'report';

	protected function _resource()
	{
		return 'posts_bulk';
	}

	// Ushahidi_Rest
	protected function _scope()
	{
		return 'posts_bulk';
	}

	// Ushahidi_Rest
	protected function _identifiers()
	{
		return parent::_identifiers() + [
			'type'      => $this->_type
		];
	}

	// Ushahidi_Rest
	protected function _payload()
	{
		return parent::_payload() + [
			'type'      => $this->_type,
		];
	}

	/**
	 * Bulk Update Posts
	 *
	 * POST /api/posts/bulk/update
	 *
	 * @return void
	 */
	public function action_post_update_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'update')
			->setPayload($this->_payload());
	}

	/**
	 * Bulk Delete Posts
	 *
	 * POST /api/posts/bulk/delete
	 *
	 * @return void
	 */
	public function action_post_delete_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'delete');
	}
}
