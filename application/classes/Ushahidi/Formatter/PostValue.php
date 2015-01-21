<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post Values
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Formatter_PostValue extends Ushahidi_Formatter_API
{
	protected $map = [];

	public function __construct($map = [])
	{
		$this->map = $map;
	}

	public function __invoke($entity)
	{
		if (isset($this->map[$entity->type]))
		{
			$formatter = $this->map[$entity->type];
			return $formatter($entity);
		}

		return $entity->value;
	}
}
