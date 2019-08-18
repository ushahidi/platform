<?php
/**
* Ushahidi Platform Send HDX Usecase
*
* @author    Ushahidi Team <team@ushahidi.com>
* @package   Ushahidi\Platform
* @copyright 2018 Ushahidi
* @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
*/

namespace Ushahidi\Core\Usecase\HXL\Organisations;

use Ushahidi\Core\Entity\UserSettingRepository;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\App\ExternalServices\HDXInterface;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Usecase;
use Log;

class GetByUser implements Usecase
{

    use AuthorizerTrait,
        FormatterTrait;
    protected $userSettingRepository;

    /**
     * Inject a repository that can create entities.
     *
     * @param  $repo ImportRepository
     * @return $this
     */
    public function setRepository($repo)
    {
        $this->repo = $repo;
        return $this;
    }

    public function setUserHXLSettingsRepository(UserSettingRepository $repo)
    {
        $this->userSettingRepository = $repo;
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

    /**
     * @return Array
     */

    public function interact()
    {
        // get user settings by user id
        $user_settings_key = $this->userSettingRepository->getConfigKeyByUser($this->auth->getUserId(), 'hdx_api_key');
        $user_settings_user_id = $this->userSettingRepository->getConfigKeyByUser(
            $this->auth->getUserId(),
            'hdx_maintainer_id'
        );
        // setup hdx interface
        $this->setHDXInterface($user_settings_key, $user_settings_user_id);
        $organisations = $this->hdxInterface->getAllOrganizationsForUser();
        if (!$organisations) {
            return $this->formatter->__invoke(null);
        }

        return $this->formatter->__invoke($organisations);
    }

    private function setHDXInterface($user_settings_key, $user_settings_user_id)
    {
        $this->hdxInterface = new HDXInterface(
            getenv('HDX_URL'),
            $user_settings_key->config_value,
            $user_settings_user_id->config_value
        );
    }
}
