<?php

/**
 * Ushahidi Config Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Traits\ArrayExchange;

class Config extends Entity
{
	public $id;

	public function setData($data)
	{
		// Config is one of the few entities that does not have a static set
		// of variables. As such, it does not require checking if properties
		// exist before setting values.
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
		return $this;
	}

	// Entity
	public function getResource()
	{
		return 'config';
	}
}
