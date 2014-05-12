<?php

/**
 * Ushahidi Media
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity as EntityInterface;
use Ushahidi\Traits\ArrayExchange;

class Media implements EntityInterface
{
	use ArrayExchange;

	public $id;
	public $user_id;
	public $caption;
	public $created;
	public $updated;
	public $mime;
	public $filename;
	public $width;
	public $height;
}
