<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter Check Lock
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Formatter;

class Ushahidi_Formatter_Post_CheckLock implements Formatter
{

	public function __invoke($lock_status)
	{ 
		return [
            'post_locked' => $lock_status
        ];
	}
}
