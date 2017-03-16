<?php

/**
 * Ushahidi Filesystem Adapter Local
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\FilesystemAdapter;

use League\Flysystem\Adapter\Local as LocalAdapter;
use Ushahidi\Core\Tool\FilesystemAdapter;

class Local implements FilesystemAdapter
{

	protected $config;

	public function __construct($config)
	{
		$this->config = $config['local'];
	}

	public function getAdapter()
	{
		return new LocalAdapter($this->config['media_upload_dir']);
	}
}
