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

class Ushahidi_Listener_IntercomCompanyListener extends AbstractListener
{
  protected $config_repo;

  public function setConfigRepo(ConfigRepository $config_repo)
  {
  		$this->config_repo = $config_repo;
  }

  public function handle(EventInterface $event, $data = null)
  {
    $intercomAppToken = getenv('INTERCOM_APP_TOKEN');
		if ($intercomAppToken) {

      $client = new IntercomClient($intercomAppToken, null);

			try {
        $company = [
          "company_id" => service('site')
        ];

        $company->custom_attributes = $data;
        // Update company
        $client->companies->create($company);

			} catch(ClientException $e) {
				Kohana::$log->add(Log::ERROR, print_r($e,true));
			}
		}
  }
}
