<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Filesystem Adapter Local
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Flysystem\Adapter\Local;
use Ushahidi\Core\Tool\FilesystemAdapter;

class Ushahidi_Filesystem_Adapter_Local implements FilesystemAdapter
{

  private $media_dir;

  public function __construct($media_dir)
  {
      $this->media_dir = $media_dir;
  }

  public function getAdapter()
  {
      return Local($this->media_dir);
  }
}

