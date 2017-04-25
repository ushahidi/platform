<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Intercom Listener
 *
 * Listens for new posts that are added to a set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Event\AbstractListener;
use League\Event\EventInterface;

use Intercom\IntercomClient;

use GuzzleHttp\Exception\ClientException;

class Ushahidi_Listener_IntercomListener extends AbstractListener
{
  public function handle(EventInterface $event, $user_email = null, $data = null)
  {
		if ($user_email) {
			$intercomAppId = service('stie.intercomAppId');

			$client = new IntercomClient($intercomAppId, null);
			try {

				$client->users->update([
					"email" => $user_email,
					"custom_attributes" => $data
				]);
			} catch(ClientException $e) {
				Kohana::$log->add(Log::ERROR, $e->getResponse());
			}
		}
  }
}
