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

use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\App\ExternalServices\HDXInterface;
use Ushahidi\Core\Entity\UserSetting;
use Ushahidi\Core\Entity\UserSettingRepository as UserSettingRepositoryContract;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository as ExportJobRepositoryContract;

class SendHXLUsecase // extends something?
{
        use AuthorizerTrait; // ? do we need this here?

        // @TODO: fetch these from the user settings when that exists
    protected $userSettings = ['hdx_server' => 'http://192.168.33.60:5000',
                                    'user_key' => 'e0371305-e830-469f-adce-56f9ff211157'];

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
        //@TODO grab the job details from ExportJobRepository
        //    $this->exportJobRepository
        //@TODO grab the metadata record from HXL/HXLMetadataRepository

        //@TODO grab the users settings from UserSettingRepository by user_id
         //   $this->UserSettingRepository

       //@TODO: then use the HDXInterface methods to attempt creation or update (as the case may be)

       //@TODO: on success, update the export_job record with SUCCESS

       //@TODO: and on failure, update the export_job record with FAILED
    }
}
