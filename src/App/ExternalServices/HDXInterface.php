<?php

namespace Ushahidi\App\ExternalServices;

/**
 * HDX Interface -
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   ExternalServices\HDX
 * @copyright 2018 Ushahidi
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\Core\Usecase\HXL\SendHXLUsecase;
use Germanazo\CkanApi\CkanApiClient;
use GuzzleHttp\Client;
use Log;

class HDXInterface
{
    protected $ckanClient;
    protected $userAPIKey;
    protected $ckanURL;

    public function __construct($url, $key)
    {
         $this->ckanURL = $url;
         $this->userAPIKey = $key;
    }

    public function setServer($url)
    {
        $this->ckanURL = $url;
    }

    public function setUserAPIKey($key)
    {
        $this->userAPIKey = $key;
    }

    // this is here so we can inject and use Guzzle unit test mocks
    public function setClientHandler($handler)
    {
        $this->handler = $handler;
    }

    public function getApiClient()
    {
        if (!isset($this->ckanClient)) {
            $config = [
                'base_uri' => $this->ckanURL,
                'headers' => ['Authorization' => $this->userAPIKey],
            ];
            //if we passed in a mock handler
            if (isset($this->handler)) {
                $config['handler'] = $this->handler;
            }
            //create a Guzzle client, used by CKAN api
            $client = new Client($config);
            $this->ckanClient = new CkanApiClient($client);
        }
        return $this->ckanClient;
    }

    // returns ID or null
    public function getDatasetIDByTitle(string $titleText)
    {
        /// setup a search query by title
        $data = [
           'q' => 'title:'.$titleText
        ];
        $datasetId = null;
        try {
            $allMatchingDatasets = $this->getApiClient()->dataset()->all($data);
        } catch (Exception $e) {
            Log::error('Unable to find HDX datasets by title '.print_r($e, true));
        }
        if ($allMatchingDatasets && array_key_exists('result', $allMatchingDatasets)) {
            if ($allMatchingDatasets['result']['count'] > 1) {
                Log::debug('Multiple datasets found: '.print_r($allMatchingDatasets['result']['count'], true));
            }
            foreach ($allMatchingDatasets['result']['results'] as $eachDataset) {
                $datasetId = $eachDataset['id'];
            }
        }
        return $datasetId;
    }

    /** Note: if error condition is the result, then we ignore it gracefully,
    * but the full error response array will be returned instead of a confirmation array
    */
    public function updateHDXDatasetRecord(array $metadata)
    {
        $apiClient = $this->getApiClient();
        $updateResult = [];
        try {
            $updateResult = $apiClient->dataset()->update($metadata);
        } catch (Exception $e) {
            // @TODO: be graceful here
            $updateResult = ['error' => 'Unable to update dataset on HDX server.'];
            Log::error('Unable to update dataset on HDX server: '.print_r($e, true));
        }
        return $updateResult;
    }

    /** Note: if error condition is the result, then we ignore it gracefully,
    * but the full error response array will be returned instead of a confirmation array
    */
    public function createHDXDatasetRecord(array $metadata)
    {
        $apiClient = $this->getApiClient();
        $createResult = [];
        try {
            $createResult = $apiClient->dataset()->create($metadata);
        } catch (Exception $e) {
            // @TODO: be graceful here
            $createResult = ['error' => 'Unable to create dataset on HDX server.'];
            Log::error('Unable to create dataset on HDX server: '.print_r($e, true));
        }
        return $createResult;
    }

    public function getAllOrganizationsForUser()
    {
        $apiClient = $this->getApiClient();
        $orgId = null;
        $data = []; // nothing to send here
        try {
            $orgResult = $apiClient->organization()->all($data);
        } catch (Exception $e) {
            // @TODO: gracefully handle this
            Log::error('Unable to get Org results '.print_r($e, true));
        }
        if (!$orgResult ||  !array_key_exists('result', $orgResult)) {
            return false;
        }
        return $orgResult['result'];
    }

    public function getOrganizationIDByName(String $organizationName)
    {
        $apiClient = $this->getApiClient();
        $orgId = null;
        $data = []; // nothing to send here
        try {
            $orgResult = $apiClient->organization()->all($data);
        } catch (Exception $e) {
            // @TODO: gracefully handle this
            Log::error('Unable to get Org results '.print_r($e, true));
        }
        // @TODO deal with this if more than one
        if ($orgResult && array_key_exists('result', $orgResult)) {
            foreach ($orgResult['result'] as $eachOrg) {
                $orgId = $eachOrg['id'];
            }
        }
        return $orgId;
    }
}
