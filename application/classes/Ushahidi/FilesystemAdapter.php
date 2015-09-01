<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi FilesystemAdapter
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\FilesystemAdapter;

abstract class Ushahidi_Filesystem_Adapter implements FilesystemAdapter
{

  private $config;
  private $media_dir;

  public function __construct($config, $media_dir)
  {
      $this->config = $config;
      $this->media_dir = $media_dir;
  }

  abstract public function getAdapter();
}

