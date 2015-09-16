<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi FilesystemAdapter
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use OpenCloud\OpenStack;
use OpenCloud\Rackspace;
use League\Flysystem\Filesystem;
use League\Flysystem\Rackspace\RackspaceAdapter as Adapter;
use Ushahidi\Core\Tool\FilesystemAdapter;

class Ushahidi_FilesystemAdapter_Rackspace implements FilesystemAdapter
{

	protected $config;

	public function __construct($config)
	{
		$this->config = $config['rackspace'];
	}

	public function getAdapter()
	{
		$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
			'username' => $this->config['username'],
			'apiKey' => $this->config['apiKey'],
		));

		$store = $client->objectStoreService(null,$this->config['region']);
		$container = $store->getContainer($this->config['container']);

		return new Adapter($container);
	}
}

