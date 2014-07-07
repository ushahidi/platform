<?php

/**
 * Ushahidi Platform Media Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

use Ushahidi\Data;

class MediaData extends Data
{
	public $file; // in $_FILES format: [name, type, size, tmp_name, error]
	public $caption;
	public $user_id;
}
