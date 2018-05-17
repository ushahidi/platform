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
        // what this needs to do...
        $job = $this->exportJobRepository->get($this->getIdentifier('job_id'));
        $user_settings = $this->userSettingRepository->getByUser($job->user_id);
        //@TODO: then use the HDXInterface methods to attempt creation or update (as the case may be)
        $this->setHDXInterface($user_settings);
        $metadata = $this->metadataRepository->getByJobId($this->getIdentifier('job_id'));
        $existing_dataset_id = $this->hdxInterface->getDatasetIDByTitle($metadata->dataset_title);
        if (!!$existing_dataset_id) {
            // TODO call update in hdx interface
        } else {
            // TODO call create in hdx interface
        }


       //@TODO: on success, update the export_job record with SUCCESS

       //@TODO: and on failure, update the export_job record with FAILED
    }


    private function setHDXInterface($user_settings)
    {
        $this->hdxInterface = new HDXInterface(
            getenv('HDX_URL'),
            $user_settings->api_key
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
