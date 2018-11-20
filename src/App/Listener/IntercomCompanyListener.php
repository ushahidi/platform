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

use Ushahidi\Core\Entity\Config;
use Ushahidi\Core\Entity\Form;

use Intercom\IntercomClient;
use GuzzleHttp\Exception\ClientException;

class IntercomCompanyListener
{

    public function handle($id, $entity)
    {
        if ($entity instanceof Form) {
            if ($entity->id === 1) {
                $changed = $entity->getChanged();
                if (isset($changed['name'])) {
                    $data = ['primary_survey_name' => $changed['name']];
                }
            }
        }

        if ($entity instanceof Config) {
            $data = [];
            if ($entity->getId() === 'site') {
                $changed = $entity->getChanged();
                // Emit Intercom Update events
                if (isset($changed['description'])) {
                    $data['has_description'] = true;
                }

                if (isset($changed['image_header'])) {
                    $data['has_logo'] = true;
                }

                // New User - set their deployment created date
                if (isset($changed['first_login'])) {
                    $data['deployment_created_date'] = date("Y-m-d H:i:s");
                }
            }

            // Intercom count datasources
            if ($entity->getId() === 'data-provider') {
                $data['num_data_sources'] = 0;
                foreach ($entity->providers as $key => $value) {
                    $value ? $data['num_data_sources']++ : null;
                }
            }
        }

        $intercomAppToken = getenv('INTERCOM_APP_TOKEN');
        $domain = service('site');

        if ($intercomAppToken && !empty($domain) && !empty($data)) {
            try {
                $client = new IntercomClient($intercomAppToken, null);

                $company = [
                    "company_id" => $domain,
                    "custom_attributes" => $data
                ];
                // Update company
                $client->companies->create($company);
            } catch (ClientException $e) {
                \Log::info($e->getMessage());
            }
        }
    }
}
