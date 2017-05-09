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

  protected $config_repo;

  public function setConfigRepo(ConfigRepository $config_repo)
  {
  		$this->config_repo = $config_repo;
  }

  public function handle(EventInterface $event, $user = null)
  {

    $config = $this->config_repo->get('thirdparty');
    $domain = Kohana::$config->load('site.client_url');

		$intercomAppToken = $config->intercomAppToken;
    $company = [
      "id" => $config->intercomCompanyId
    ]

		if ($user && $config->intercomAppToken) {
      $client = new IntercomClient($config->intercomAppToken, null);

			try {
        // Get Company if it already exists, if not create it
        if (!$config->intercomCompanyId) {

          $site_name = Kohana::$config->load('site.name') ?: 'Ushahidi';
          $created = date("Y-m-d H:i:s");

          $company = $client->companies->create([
            "company_id" => $domain,
            "name" => $site_name,
            "custom_attributes" = [
              "created" => $created
            ]
          ]);

          $config->intercomCompanyId = $company->id;
          $this->config_repo->update($config);
        }

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
