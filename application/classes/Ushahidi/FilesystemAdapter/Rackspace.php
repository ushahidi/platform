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

  public function getAdapter()
  {
      $client = new Rackspace(Rackspace::UK_IDENTITY_ENDPOINT, array(
          'username' => ':username',
          'apiKey' => ':password',
      ));

      $store = $client->objectStoreService('cloudFiles', 'LON');
      $container = $store->getContainer('flysystem');

      return new Adapter($container);
  }
}

