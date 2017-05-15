<?php

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

namespace Ushahidi\App\Listener;

use League\Event\AbstractListener;
use League\Event\EventInterface;

use Intercom\IntercomClient;

use GuzzleHttp\Exception\ClientException;

class IntercomListener extends AbstractListener
{
  public function handle(EventInterface $event, $user_email = null, $data = null)
  {
		$intercomAppToken = service('site.intercomAppToken');

		if ($user_email && $intercomAppToken) {

			$client = new IntercomClient($intercomAppToken, null);
			try {

				$client->users->update([
					"email" => $user_email,
					"custom_attributes" => $data
				]);
			} catch(ClientException $e) {
				\Kohana::$log->add(Log::ERROR, print_r($e,true));
			}
		}
  }
}
