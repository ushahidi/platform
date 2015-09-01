<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi FilesystemAdapter Local
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Flysystem\Adapter\Local;

abstract class Ushahidi_Filesystem_Adapter_Local extends Ushahidi_Filesystem_Adapter
{

  public function getAdapter()
  {
      return Local($this->media_dir);
  }
}

