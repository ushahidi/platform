<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi FilesystemAdapter AWS
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Ushahidi\Core\FilesystemAdapter;

class Ushahidi_Filesystem_Adapter_AWS implements FilesystemAdapter
{

  public function getAdapter($config, $media_dir)
  {
      $client = new S3Client([
          'credentials' => [
              'key'    => $this->config->key,
              'secret' => $this->config->secret
          ],
          'region' =>  $this->config->region,
          'version' => $this->config->version,
      ]);

      $adapter = new AwsS3Adapter($client, $this->config->bucket_name);

      return $adpater;
  }
}

