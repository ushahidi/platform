<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter Get Lock
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Formatter;

class Ushahidi_Formatter_Post_GetLock implements Formatter
{

	public function __invoke($lock_id)
	{ 
		return [
            'id' => $lock_id
        ];
	}
}
