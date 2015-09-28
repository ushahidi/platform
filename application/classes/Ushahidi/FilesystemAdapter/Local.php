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

class Ushahidi_FilesystemAdapter_Local implements FilesystemAdapter
{

	protected $config;

	public function __construct($config)
	{
		$this->config = $config['local'];
	}

	public function getAdapter()
	{
		return new Local($this->config['media_upload_dir']);
	}
}

