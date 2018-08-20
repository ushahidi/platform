
<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ushahidi API Form Stats Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Forms_Stats extends Ushahidi_Rest {
    
    protected $_action_map = array
    (
        Http_Request::GET    => 'get',
        Http_Request::OPTIONS => 'options'
    );
    protected function _scope()
    {
        return 'forms';
    }

	protected function _resource()
	{
		return 'form_stats';
	}

	// Get Lock
	public function action_get_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'search');
        $this->_usecase->setIdentifiers($this->request->param());
        $this->_usecase->setFilters($this->request->query() + [
            'created_before' => $this->request->param('created_before'),
            'created_after' => $this->request->param('created_after'),
			]);
		$this->_usecase
			->setFormatter(service("formatter.entity.form.stats"));
	}
}