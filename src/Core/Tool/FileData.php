<?php

/**
 * Ushahidi Platform File Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Data;

class FileData extends Data
{
	/**
	 * @var String $file filesystem path
	 */
	public $file;

	/**
	 * @var String $type MIME type
	 */
	public $type;

	/**
	 * @var Integer $size in bytes
	 */
	public $size;

	/**
	 * @var Integer $width image width (if image)
	 */
	public $width;

	/**
	 * @var Integer $height image height (if image)
	 */
	public $height;
}
