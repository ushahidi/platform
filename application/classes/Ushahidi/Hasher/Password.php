<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Password Hasher
 *
 * Implemented using A1
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Hasher;

class Ushahidi_Hasher_Password implements Hasher
{
	private $a1;

	public function __construct()
	{
		$this->a1 = A1::instance();
	}

	public function hash($password)
	{
		return $this->a1->hash($password);
	}
}
