<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ushahidi API Post Lock Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_API_Posts_ChangeLog extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'posts';
	}
	protected function _resource()
	{
		return 'posts_changelog';
	}

	// Ushahidi_Rest
	public function action_post_index_collection()
	{
		parent::action_post_index_collection();

		$this->_usecase->setIdentifiers($this->request->param());
	}


	// Ushahidi_Rest
	public function action_get_index_collection()
	{
		parent::action_get_index_collection();

		$this->_usecase->setIdentifiers($this->request->param());
		$this->_usecase->setFilters($this->request->query() + [
			'post_id' => $this->request->param('post_id')
			]);
	}


}
