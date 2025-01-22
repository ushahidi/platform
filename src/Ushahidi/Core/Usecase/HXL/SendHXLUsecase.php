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

use Illuminate\Support\Facades\Log;
use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Usecase\Concerns\Translator as TranslatorTrait;
use Ushahidi\Core\Tool\ExternalServices\HDXInterface;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;
use Ushahidi\Contracts\Repository\Entity\HXLLicenseRepository;
use Ushahidi\Contracts\Repository\Entity\HXLMetadataRepository;
use Ushahidi\Contracts\Repository\Entity\UserSettingRepository;
use Ushahidi\Contracts\Repository\Entity\HXLFormAttributeHXLAttributeTagRepository;

class SendHXLUsecase implements Usecase
{
    use IdentifyRecords;
    use AuthorizerTrait; // ? do we need this here?
    use FormatterTrait;
    use TranslatorTrait;
    protected $metadataRepository;
    protected $userSettingRepository;
    protected $exportJobRepository;
    protected $licenseRepository;
    protected $formAttributeHXLAttributeTagRepository;
    protected $jobID;
    protected $hdxInterface;

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
        $isHdxConfigured = $this->setHDXInterface($user_settings_key, $user_settings_user);

        // If HDX isn't configured, just exit now
        if (!$isHdxConfigured) {
            $updated_job = $this->setJobStatusAndUpdate($job, "FAILED");
            return $this->formatter->__invoke($updated_job);
        }

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

        $existing_dataset_id = $this->hdxInterface->getDatasetIDByName(
            $metadata->dataset_title,
            $metadata->organisation_name
        );

        if (!!$existing_dataset_id) {
            $updated_job = $this->updateDatasetAndResource($existing_dataset_id, $metadata, $job, $license, $tags);
        } else {
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
        $dataset_result = $this->hdxInterface->createHDXDatasetRecord($metadata->asArray(), $license, $tags);
        if (isset($dataset_result['error'])) {
            Log::error('Dataset creation error: ' . var_export($dataset_result, true));
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
        $resource_result = $this->hdxInterface->createResourceForDataset(
            $job->id,
            $dataset_id,
            $job->url,
            $metadata->dataset_title
        );
        if (isset($resource_result['error'])) {
            Log::error("Resource creation error for job", ['job_id' => $job->id, 'result' => $resource_result]);
            $job = $this->setJobStatusAndUpdate($job, "FAILED");
            return $job;
        }
        $job = $this->setJobStatusAndUpdate($job, "SUCCESS");
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
        $this->exportJobRepository->update($job);
        return $job;
    }

    private function setHDXInterface($user_settings_key, $user_settings_user_id)
    {
        $hdx_url = getenv('HDX_URL');
        // If any config values aren't set, fail
        if (empty($hdx_url) || empty($user_settings_key->config_value) || empty($user_settings_user_id->config_value)) {
            return false;
        }

        $this->hdxInterface = new HDXInterface(
            getenv('HDX_URL'),
            $user_settings_key->config_value,
            $user_settings_user_id->config_value
        );

        return true;
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
