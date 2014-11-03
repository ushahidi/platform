<?php

/**
 * Ushahidi Platform Upload Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Data;

class UploadData extends Data
{
	/**
	 * @var String  $name original file name
	 */
	public $name;

	/*
	 * @var String $type MIME type (not reliable!)
	 */
	public $type;

	/*
	 * @var Integer $size in bytes
	 */
	public $size;

	/*
	 * @var Integer $tmp_name temporary filesystem path
	 */
	public $tmp_name;

	/*
	 * @var Integer $error PHP error code
	 */
	public $error;
}
