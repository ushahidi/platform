<?php

/**
 * Ushahidi Platform Send HDX Usecase
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2018 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\HXL;

use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Entity\HXL\HXLFormAttributeHXLAttributeTagRepository;
use Ushahidi\Core\Entity\HXL\HXLLicenseRepository;
use Ushahidi\Core\Entity\HXL\HXLMetadataRepository;
use Ushahidi\Core\Entity\UserSettingRepository;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\App\ExternalServices\HDXInterface;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Usecase;
use Log;

class SendHXLUsecase implements Usecase
{
    use Usecase\Concerns\IdentifyRecords;
    use AuthorizerTrait; // ? do we need this here?
    use FormatterTrait;
    protected $metadataRepository;
    protected $userSettingRepository;
    protected $exportJobRepository;
    protected $licenseRepository;
    protected $formAttributeHXLAttributeTagRepository;
    protected $jobID;
    protected $hdxInterface;


        // @TODO: fetch these from the user settings when that exists
    protected $userSettings = [
        'hdx_server' => 'http://192.168.33.60:5000',
        'user_key' => 'e0371305-e830-469f-adce-56f9ff211157'
    ];

    public function setRepository($repo)
    {
        return $this;
    }

    public function setExportJobRepository(ExportJobRepository $repo)
    {
        $this->exportJobRepository = $repo;
    }

    public function setUserHXLSettingsRepository(UserSettingRepository $repo)
    {
        $this->userSettingRepository = $repo;
    }

    public function setHXLMetadataRepository(HXLMetadataRepository $repo)
    {
        $this->metadataRepository = $repo;
    }

    public function setHXLLicenseRepository(HXLLicenseRepository $repo)
    {
        $this->licenseRepository = $repo;
    }
    public function setHXLFormAttributeHXLAttributeTagRepository(HXLFormAttributeHXLAttributeTagRepository $repo)
    {
        $this->formAttributeHXLAttributeTagRepository = $repo;
    }
    public function setJobID($jobID)
    {
        $this->jobID = $jobID;
    }

    public function interact()
    {
        // get job by job_id
        $job = $this->exportJobRepository->get($this->getIdentifier('job_id'));
        // get user settings by user id
        $user_settings_key = $this->userSettingRepository->getConfigKeyByUser($job->user_id, 'hdx_api_key');
        $user_settings_user = $this->userSettingRepository->getConfigKeyByUser($job->user_id, 'hdx_maintainer_id');
        // setup hdx interface
        $this->setHDXInterface($user_settings_key, $user_settings_user_id);
        // get metadata by job id
        $metadata = $this->metadataRepository->get($job->hxl_meta_data_id);
        // get license by metadata->license_id
        $license = $this->licenseRepository->get($metadata->license_id);
        // get all the tags assigned to this hxl export job's data
        $tags = $this->formAttributeHXLAttributeTagRepository->getHxlTags($job);
        $tags = array_map(function ($tag) {
            return ['name' => $tag['tag_name']];
        }, $tags);
        // check if the dataset exists to decide if we update or create one
        $existing_dataset_id = $this->hdxInterface->getDatasetIDByName($metadata->dataset_title);


        if (!!$existing_dataset_id) {
            Log::debug('Found dataset' . print_r($metadata->dataset_title, true));
            $updated_job = $this->updateDatasetAndResource($existing_dataset_id, $metadata, $job, $license, $tags);
        } else {
            Log::debug('Did not find dataset' . print_r($metadata->dataset_title, true));
            $updated_job = $this->createDatasetAndResource($metadata, $job, $license, $tags);
        }
        return $this->formatter->__invoke($updated_job);
    }

    /**
     * @param $dataset_id
     * @param $metadata
     * @param $job
     * @param $license
     * @param $tags
     * @return mixed
     */
    private function updateDatasetAndResource($dataset_id, $metadata, $job, $license, $tags)
    {
        $dataset_result = $this->hdxInterface->updateHDXDatasetRecord(
            $dataset_id,
            $metadata->asArray(),
            $license,
            $tags
        );
        if (isset($dataset_result['error'])) {
            $job = $this->setJobStatusAndUpdate($job, 'FAILED');
            return $job;
        }
        return $this->createResourceAndUpdateJob($dataset_id, $job, $metadata);
    }
    /**
     * @param $metadata
     * @param $job
     * @param $license
     * @param $tags
     * @return mixed
     */
    private function createDatasetAndResource($metadata, $job, $license, $tags)
    {
        Log::debug('createdatasetandresource');
        $dataset_result = $this->hdxInterface->createHDXDatasetRecord($metadata->asArray(), $license, $tags);
        Log::debug('createdatasetandresource happened');
        if (isset($dataset_result['error'])) {
            Log::debug('Dataset resulted in error: '.print_r($dataset_result, true));

            $job = $this->setJobStatusAndUpdate($job, 'FAILED');
            return $job;
        }

        return $this->createResourceAndUpdateJob($dataset_result['result']['id'], $job, $metadata);
    }

    /**
     * @param $dataset_id
     * @param $job
     * @param $metadata
     * @return mixed
     */
    private function createResourceAndUpdateJob($dataset_id, $job, $metadata)
    {
        Log::debug('Dataset job' . var_export($job->asArray(), true));
        $resource_result = $this->hdxInterface->createResourceForDataset(
            $dataset_id,
            $job->url,
            $metadata->dataset_title
        );
        if (isset($resource_result['error'])) {
            Log::debug('Resource is an error: '.print_r($resource_result, true));
            $job = $this->setJobStatusAndUpdate($job, 'FAILED');
            return $job;
        }
        Log::debug('Resource success: '.print_r($resource_result, true));
        $job = $this->setJobStatusAndUpdate($job, 'SUCCESS');
        return $job;
    }
    /**
     * @param $job
     * @param $status
     * @return mixed
     */
    private function setJobStatusAndUpdate($job, $status)
    {
        $job->setState(['status' => $status]);
        Log::debug('Job updated:' . print_r($job, true));
        $this->exportJobRepository->update($job);
        return $job;
    }

    private function setHDXInterface($user_settings_key, $user_settings_user_id)
    {
        $this->hdxInterface = new HDXInterface(
            getenv('HDX_URL'),
            $user_settings_key->config_value,
            $user_settings_user_id->config_value
        );
    }
    /**
     * Will this usecase write any data?
     *
     * @return Boolean
     */
    public function isWrite()
    {
        return false;
    }

    /**
     * Will this usecase search for data?
     *
     * @return Boolean
     */
    public function isSearch()
    {
        return false;
    }
}
