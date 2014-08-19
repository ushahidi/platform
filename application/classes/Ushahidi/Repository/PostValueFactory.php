<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Repository_PostValueFactory
{
	// a map of value type to factory closures
	protected $map = array();

	public function __construct($map = array())
	{
		$this->map = $map;
	}

	public function getRepo($type)
	{
		return $this->map[$type]();
	}

	public function proxy()
	{
		return new Ushahidi_Repository_PostValueProxy($this);
	}

	public function each($callback)
	{
		foreach ($this->map as $type => $class)
		{
			$repo = $this->getRepo($type);
			$callback($repo);
		}
	}
}