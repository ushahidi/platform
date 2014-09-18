<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Validator Factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_Post_ValueFactory
{
	// a map of value type to factory closures
	protected $map = array();

	public function __construct($map = array())
	{
		$this->map = $map;
	}

	public function getValidator($type)
	{
		return isset($this->map[$type]) ? $this->map[$type]() : FALSE;
	}
}