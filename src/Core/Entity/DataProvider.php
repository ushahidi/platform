<?php

/**
 * Ushahidi Data Provider Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Traits\ArrayExchange;

class DataProvider extends Entity
{
	public $id;
	public $name;
	public $version;
	public $services;
	public $links;
	public $options;

	// Entity
	public function getResource()
	{
		return 'dataprovider';
	}
}
