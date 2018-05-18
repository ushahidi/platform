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

use Germanazo\CkanApi\Repositories\BaseRepository;
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
            Log::debug('Api client config: '.print_r($config, true));
            //if we passed in a mock handler
            if (isset($this->handler)) {
                $config['handler'] = $this->handler;
            }
            //create a Guzzle client, used by CKAN api
            $client = $this->getHttpClient($config);
            $this->ckanClient = new CkanApiClient($client);
        }
        return $this->ckanClient;
    }
    private function getHttpClient($config)
    {
        return new Client($config);
    }

    // returns ID or null
    public function getDatasetIDByName($title)
    {
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));
        $datasetId = null;
        try {
            $dataset = $this->getApiClient()->dataset()->show($slug);
            $datasetId = isset($dataset['result']) && isset($dataset['result']['id']) ?
                $dataset['result']['id'] : null;
        } catch (Exception $e) {
            Log::error('Unable to find HDX datasets by title '.print_r($e, true));
        }

        return $datasetId;
    }

    /**
     * @param array $metadata
     * @param $license
     * @param array $tags
     * @return array
     * Create dataset object based on the parameters we received from create/update
     */
    private function formatDatasetObject(array $metadata, $license, $tags = [])
    {
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $metadata['dataset_title']));

        return $dataset = array(
            "name" =>  $slug, //FIXME should it be user input?
            "author" => $metadata['maintainer'],
            "maintainer" => $metadata['maintainer'],
            "organization" => $metadata['organisation'],
            "private" => $metadata['private'],
            "owner_org" => $metadata['organisation'],
            "title" => $metadata['dataset_title'],
            "dataset_source" =>  $metadata['source'],
            "data_update_frequency" => "1", //1 day. TODO add frequency to metadata
            "methodology" => "other", //TODO add methodology to metadata
            "tags" => $tags, //[{"name":"coordinates"}],
            "license_id" => $license->code,
            "allow_no_resources" => true
        );
    }

    /** Note: if error condition is the result, then we ignore it gracefully,
    * but the full error response array will be returned instead of a confirmation array
    */
    public function updateHDXDatasetRecord($dataset_id, array $metadata, $license, $tags = [])
    {
        $dataset = $this->formatDatasetObject($metadata, $license, $tags);
        $dataset['id'] = $dataset_id;
        $apiClient = $this->getApiClient();
        $updateResult = [];
        try {
            $updateResult = $apiClient->dataset()->update($dataset);
        } catch (Exception $e) {
            // @TODO: be graceful here
            $updateResult = ['error' => 'Unable to update dataset on HDX server.'];
            Log::error('Unable to update dataset on HDX server: '.print_r($e, true));
        }
        return $updateResult;
    }
    /**
     * @param array $metadata
     * @param $license
     * @param array $tags
     * @return array
     * Note: if error condition is the result, then we ignore it gracefully,
     * but the full error response array will be returned instead of a confirmation array
     */
    public function createHDXDatasetRecord(array $metadata, $license, $tags = [])
    {
        $dataset = $this->formatDatasetObject($metadata, $license, $tags);
        $apiClient = $this->getApiClient();
        $createResult = [];
        try {
            $createResult = $apiClient->dataset()->create($dataset);
        } catch (Exception $e) {
            // @TODO: be graceful here
            $createResult = ['error' => 'Unable to create dataset on HDX server.'];
            Log::error('Unable to create dataset on HDX server: '.print_r($e, true));
        }
        return $createResult;
    }

    /**
     * @param array $metadata
     * @param $license
     * @param array $tags
     * @return array
     * Note: if error condition is the result, then we ignore it gracefully,
     * but the full error response array will be returned instead of a confirmation array
     */
    public function createResourceForDataset($package_id, $job_url, $dataset_title)
    {
        $resource = [
            'package_id' => $package_id,
            'url' => $job_url,
            'resource_type' => 'csv',
            'name' => $dataset_title
        ];
        $apiClient = $this->getApiClient();
        $createResult = [];
        try {
            $createResult = $apiClient->resource()->create($resource);
        } catch (Exception $e) {
            // @TODO: be graceful here
            $createResult = ['error' => 'Unable to create resource on HDX server.'];
            Log::error('Unable to create resource on HDX server: '.print_r($e, true));
        }
        return $createResult;
    }

    // TODO test
    public function getAllOrganizationsForUser()
    {
        try {
            $orgResult = $this->ckanGetOrganizationListForUser();
            if (isset($orgResult['result'])) {
                $orgResult = $orgResult['result'];
            }
        } catch (Exception $e) {
            $orgResult = false;
            // @TODO: gracefully handle this
            Log::error('Unable to get Org results '.print_r($e, true));
        }
        return $orgResult;
    }

    /**
     * Custom request to ckan api since Laravel CKAN API library does not
     * include the organization_list_for_user action and adding the new repository to
     * the germanazzo namespace results in issues due to the endpoint formatting it does
     * (it adds _list to the end of the action and breaks the request)
     * @return mixed
     */
    private function ckanGetOrganizationListForUser()
    {
        $config = [
            'base_uri' => $this->ckanURL,
            'headers' => ['Authorization' => $this->userAPIKey],
        ];
        //if we passed in a mock handler
        if (isset($this->handler)) {
            $config['handler'] = $this->handler;
        }
        $apiClient = $this->getHttpClient($config);
        try {
            $request = $apiClient->get("$this->ckanURL/api/action/organization_list_for_user");
            $requestBody = json_decode($request->getBody()->getContents(), true);

            if ($request->getStatusCode() != 200 || (bool) $requestBody['success'] == false) {
                return false;
            }
            return $requestBody;
        } catch (\Exception $e) {
            return false;
        }

        return $results;
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
