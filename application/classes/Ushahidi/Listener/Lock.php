<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Lock Listener
 *
 * Listens for new lock events
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Event\AbstractListener;
use League\Event\EventInterface;

class Ushahidi_Listener_Lock extends AbstractListener
{

  public function handle(EventInterface $event, $user_id = null, $event_type = null)
  {
      if ($user_id) {
          $url = getenv('REDIS_URL');
          $port = getenv('REDIS_PORT');
          if ($url && $port) {
              $redis = new Redis();

              $redis->connect($url, $port);

              $redis->publish($user_id . '-lock', $event_type);
              
              $redis->close();
          }
      }
  }
}
