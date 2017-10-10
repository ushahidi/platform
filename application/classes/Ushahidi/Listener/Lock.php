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
use Ushahidi\Core\Traits\RedisFeature;
use Ushahidi\Core\Traits\UserContext;

class Ushahidi_Listener_Lock extends AbstractListener
{
    // Provides getUser()
	use UserContext;

    use RedisFeature;

    public function handle(EventInterface $event, $user_id = null, $event_type = null)
    {
        $user = $this->getUser();
        // Check if the webhooks feature enabled
        if (!$this->isRedisEnabled()) {
            return false;
        }
        Kohana::$log->add(Log::ERROR, print_r('listener', true));

        if ($user_id) {
            
            $url = getenv('REDIS_URL');
            $port = getenv('REDIS_PORT');
            $redis_channel = getenv('REDIS_CHANNEL');

            if ($url && $port) {
                $redis = new Redis();

                $event = json_encode([
                    "channel" => $user_id . '-lock',
                    "message" => 'lock_broken'
                ]);
                
                $redis->connect($url, $port);

                $redis->publish($redis_channel, $event);
                
                $redis->close();
            }
        }
    }
}
