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
use Ushahidi\Core\Entity\HXL\HXLMetadataRepository;
use Ushahidi\Core\Entity\UserSettingRepository;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\App\ExternalServices\HDXInterface;
use Ushahidi\Core\Usecase;

class SendHXLUsecase implements Usecase
{
    use Usecase\Concerns\IdentifyRecords;
    use AuthorizerTrait; // ? do we need this here?
    protected $metadataRepository;
    protected $userSettingRepository;
    protected $exportJobRepository;
    protected $jobID;
    protected $hdxInterface;

        // @TODO: fetch these from the user settings when that exists
    protected $userSettings = [
        'hdx_server' => 'http://192.168.33.60:5000',
        'user_key' => 'e0371305-e830-469f-adce-56f9ff211157'
    ];

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

    public function setJobID($jobID)
    {
        $this->jobID = $jobID;
    }

    public function interact()
    {
        // get job by job_id
        $job = $this->exportJobRepository->get($this->getIdentifier('job_id'));
        // get user settings by user id
        $user_settings = $this->userSettingRepository->getByUser($job->user_id);
        // setup hdx interface
        $this->setHDXInterface($user_settings);
        // get metadata by job id
        $metadata = $this->metadataRepository->getByJobId($this->getIdentifier('job_id'));
        // check if the dataset exists to decide if we update or create one
        $existing_dataset_id = $this->hdxInterface->getDatasetIDByTitle($metadata->dataset_title);
        // TODO grab the tags assigned by the user to add them to the dataset's resource
        // TODO Add resource creation
        if (!!$existing_dataset_id) {
            $updated_job = $this->updateDataset($metadata, $job);
        } else {
            $updated_job = $this->createDataset($metadata, $job);
        }
        return $this->formatter->__invoke($updated_job);
    }

    private function updateDataset($metadata, $job)
    {
        $results = $this->hdxInterface->updateHDXDatasetRecord($metadata->asArray());
        if (isset($results['error'])) {
            $job->setState(['status' => 'FAILED']);
        } else {
            $job->setState(['status' => 'SUCCESS']);
        }
        $this->exportJobRepository->update($job);
        return $job;
    }

    private function createDataset($metadata, $job)
    {
        $results = $this->hdxInterface->createHDXDatasetRecord($metadata->asArray());
        if (isset($results['error'])) {
            $job->setState(['status' => 'FAILED']);
        } else {
            $job->setState(['status' => 'SUCCESS']);
        }
        $this->exportJobRepository->update($job);
        return $job;
    }

    private function setHDXInterface($user_settings)
    {
        $this->hdxInterface = new HDXInterface(
            getenv('HDX_URL'),
            $user_settings->config_value
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
