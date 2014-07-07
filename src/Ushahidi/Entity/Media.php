<?php

/**
 * Ushahidi Media Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class Media extends Entity
{
	public $id;
	public $user_id;
	public $caption;
	public $created;
	public $updated;
	public $mime;
	public $o_filename;
	public $o_size;
	public $o_width;
	public $o_height;

	public function getResource()
	{
		return 'media';
	}
}
