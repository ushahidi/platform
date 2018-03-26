
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
        return 'stats';
    }
}