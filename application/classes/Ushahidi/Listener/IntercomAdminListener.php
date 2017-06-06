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

class Ushahidi_Listener_IntercomAdminListener extends AbstractListener
{

  public function handle(EventInterface $event, $user = null)
  {
    if($user && $user->role === 'admin') {
      $intercomAppToken = getenv('INTERCOM_APP_TOKEN');
      $domain = service('site');
      $company = [
        "id" => $domain
      ];

      if ($intercomAppToken) {
        $client = new IntercomClient($intercomAppToken, null);

        try {
          $client->users->update([
            "email" => $user->email,
            "created_at" => $user->created,
            "user_id" => $domain . '_' . $user->id,
            "realname" => $user->realname,
            "last_login" => $user->last_login,
            "role" => $user->role,
            "language" => $user->language,
            "companies" => [
              $company
            ]
          ]);
        } catch(ClientException $e) {
          Kohana::$log->add(Log::ERROR, print_r($e,true));
        }
      }
    }
  }
}
