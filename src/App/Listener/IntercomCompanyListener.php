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

use Intercom\IntercomClient;
use League\Event\EventInterface;
use League\Event\AbstractListener;
use Illuminate\Support\Facades\Log;
use Ushahidi\App\Multisite\UsesSiteInfo;
use GuzzleHttp\Exception\ClientException;

class IntercomCompanyListener extends AbstractListener
{
    use UsesSiteInfo;

    public function handle(EventInterface $event, $data = null)
    {
        $intercomAppToken = getenv('INTERCOM_APP_TOKEN');
        $domain = $this->getSite()->getBaseUri();

        if ($intercomAppToken && !empty($domain)) {
            try {
                $client = new IntercomClient($intercomAppToken, null);

                $company = [
                    "company_id" => $domain,
                    "custom_attributes" => $data
                ];
                // Update company
                $client->companies->create($company);
            } catch (ClientException $e) {
                Log::info($e->getMessage());
            }
        }
    }
}
