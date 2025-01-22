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

class UploadData extends Data
{
    /**
     * @var string  $name original file name
     */
    public $name;

    /**
     * @var string $type MIME type (not reliable!)
     */
    public $type;

    /**
     * @var integer $size in bytes
     */
    public $size;

    /**
     * @var integer $tmp_name temporary filesystem path
     */
    public $tmp_name;

    /**
     * @var integer $error PHP error code
     */
    public $error;
}
